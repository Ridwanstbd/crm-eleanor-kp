<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\MessageTemplate;

class CampaignController extends Controller
{
    // Menampilkan daftar kampanye
    public function index()
    {
        $campaigns = Campaign::all();
        return view('pages.Admin.Campaign.index', compact('campaigns'));
    }

    // Menampilkan form untuk menambah kampanye
    public function create()
    {
        return view('pages.Admin.Campaign.create');
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

    // Menampilkan form untuk mengedit kampanye
    public function edit(Campaign $campaign)
    {
        return view('pages.Admin.Campaign.edit', compact('campaign'));
    }

    // Memperbarui kampanye
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
