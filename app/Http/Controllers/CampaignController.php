<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\MessageTemplate;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::all();
        return view('pages.Admin.Campaign.index', compact('campaigns'));
    }

    public function create()
    {
        $products = Product::all();
        $templates = MessageTemplate::all();
        return view('pages.Admin.Campaign.create', compact("products","templates"));
    }

    // Menyimpan kampanye baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'schedule' => 'required|date',
        ]);

        Campaign::create($request->only('name', 'schedule'));

        return redirect()->route('campaigns.index')->with('success', 'Kampanye berhasil dibuat.');
    }

    public function edit(Campaign $campaign)
    {
        return view('pages.Admin.Campaign.show', compact('campaign'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required',
            'schedule' => 'required|date',
        ]);

        $campaign->update($request->only('name', 'schedule'));

        return redirect()->route('campaigns.index')->with('success', 'Kampanye berhasil diperbarui.');
    }

    // Menghapus kampanye
    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Kampanye berhasil dihapus.');
    }
}
