<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;

class CustomerProductPurchaseController extends Controller
{
    public function index()
    {
        return view('pivot.product_purchases.index', [
            'customers' => Customer::with('purchases')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'last_purchase_quantity' => 'required|integer|min:1',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $customer->purchases()->syncWithoutDetaching([
            $request->product_id => ['last_purchase_quantity' => $request->last_purchase_quantity]
        ]);

        return back()->with('success', 'Product added to customer.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'last_purchase_quantity' => 'required|integer|min:1',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $customer->purchases()->updateExistingPivot(
            $request->product_id,
            ['last_purchase_quantity' => $request->last_purchase_quantity]
        );

        return back()->with('success', 'Purchase quantity updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $customer->purchases()->detach($request->product_id);

        return back()->with('success', 'Product removed from customer.');
    }
}
