<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;
use App\Models\Campaign;
use App\Models\MessageTemplate;
use App\Models\Product;
use App\Models\CustomerGroup;
use App\Models\Order; // Pastikan Anda memiliki model Order/Purchase

class FonnteController extends Controller
{
    /**
     * Menjadwalkan pesan pengingat untuk produk tertentu dari kelompok konsumen tertentu.
     * Pesan akan terkirim pada tanggal estimasi habis berdasarkan jumlah pembelian produk.
     *
     * @param \App\Models\Campaign $campaign // Campaign ini membawa template pesan
     * @param string $productSlug // Slug atau nama unik produk (misal: 'susu', 'roti')
     * @param string $customerGroupName // Nama kelompok konsumen (misal: 'Shopee', 'Website Customers')
     * @return \Illuminate\Http\Response
     */
    public function scheduleProductReminderMessages(Campaign $campaign, string $productSlug, string $customerGroupName)
    {
        // Pastikan campaign memiliki token Fonnte dan template pesan
        $fonnteToken = $campaign->shop->fonnte_token; 
        $messageTemplateContent = $campaign->messageTemplate->content;

        // Mendapatkan produk berdasarkan slug/nama
        $targetProduct = Product::where('slug', $productSlug)
                               ->orWhere('name', $productSlug) // Coba cari berdasarkan nama juga
                               ->first();
        if (!$targetProduct) {
            return response()->json(['message' => "Produk '{$productSlug}' tidak ditemukan."], 404);
        }

        // Mendapatkan kelompok konsumen
        $targetCustomerGroup = CustomerGroup::where('name', $customerGroupName)->first();
        if (!$targetCustomerGroup) {
            return response()->json(['message' => "Kelompok konsumen '{$customerGroupName}' tidak ditemukan."], 404);
        }

        // Ambil semua pelanggan dari kelompok target
        $targetCustomerIds = $targetCustomerGroup->customers->pluck('id')->toArray();

        // Ambil pembelian produk target hari ini dari konsumen kelompok target
        $today = now()->toDateString();
        $productPurchasesToday = Order::whereIn('customer_id', $targetCustomerIds)
                                      ->where('product_id', $targetProduct->id)
                                      ->whereDate('order_date', $today)
                                      ->with('customer') 
                                      ->get();

        if ($productPurchasesToday->isEmpty()) {
            return response()->json(['message' => "Tidak ada pembelian '{$targetProduct->name}' dari konsumen '{$customerGroupName}' hari ini."], 200);
        }

        $messagesToSend = [];

        foreach ($productPurchasesToday as $purchase) {
            $customer = $purchase->customer;
            $quantityPurchased = $purchase->quantity;

            $estimationDays = $quantityPurchased; 

            // Jika produk memiliki estimasi hari default (misal: susu biasanya habis dalam 7 hari per unit)
            // Anda bisa menambahkan kolom `default_estimation_days` di tabel `products`.
            // if ($targetProduct->default_estimation_days) {
            //     $estimationDays = $quantityPurchased * $targetProduct->default_estimation_days;
            // }

            // Contoh yang lebih kompleks: Ambil estimasi habis dari order item jika ada
            // $estimationDays = $purchase->estimated_days_to_finish ?? $quantityPurchased; 
            // -----------------------------------------------------------
            
            // Hitung tanggal dan timestamp pengiriman pesan
            $scheduledDate = now()->addDays($estimationDays);
            $scheduleTimestamp = $scheduledDate->timestamp;
            $formattedEstimationDate = $scheduledDate->format('d M Y');

            // Ganti placeholder dalam template pesan
            // Anda bisa menambahkan placeholder lebih banyak di sini sesuai kebutuhan Anda
            $personalizedMessage = str_replace(
                [
                    '{name}', 
                    '{product_name}',             // Nama produk yang dibeli
                    '{quantity_purchased}',       // Jumlah produk yang dibeli
                    '{estimated_finish_date}'     // Tanggal estimasi habis
                ], 
                [
                    $customer->name, 
                    $targetProduct->name,            
                    $quantityPurchased,            
                    $formattedEstimationDate       
                ],
                $messageTemplateContent
            );

            $messagesToSend[] = [
                "target" => '62' . $customer->phone,
                "message" => $personalizedMessage,
                "schedule" => $scheduleTimestamp, 
                "delay" => "3", 
            ];
        }

        $payload = [
            "data" => json_encode($messagesToSend),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $fonnteToken,
            ])->post('https://api.fonnte.com/send', $payload);

            if ($response->successful()) {
                return response()->json(['message' => "Pesan pengingat untuk '{$targetProduct->name}' berhasil dijadwalkan.", 'response' => $response->json()], 200);
            } else {
                return response()->json(['message' => "Gagal menjadwalkan pesan pengingat untuk '{$targetProduct->name}'.", 'error' => $response->json()], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat menghubungi Fonnte API.', 'error' => $e->getMessage()], 500);
        }
    }
}