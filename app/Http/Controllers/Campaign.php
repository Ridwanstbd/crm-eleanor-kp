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
        return view('campaigns.index', ['campaigns' => Campaign::with(['product', 'messageTemplate'])->get()]);
    }

    public function create()
    {
        return view('campaigns.create', [
            'products' => Product::all(),
            'templates' => MessageTemplate::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'product_id' => 'required|exists:products,id',
            'message_template_id' => 'required|exists:message_templates,id',
            'schedule' => 'nullable|date',
        ]);

        Campaign::create($request->only('name', 'product_id', 'message_template_id', 'schedule'));

        return redirect()->route('campaigns.index')->with('success', 'Campaign created.');
    }

    public function edit(Campaign $campaign)
    {
        return view('campaigns.edit', [
            'campaign' => $campaign,
            'products' => Product::all(),
            'templates' => MessageTemplate::all(),
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required',
            'product_id' => 'required|exists:products,id',
            'message_template_id' => 'required|exists:message_templates,id',
            'schedule' => 'nullable|date',
        ]);

        $campaign->update($request->only('name', 'product_id', 'message_template_id', 'schedule'));

        return redirect()->route('campaigns.index')->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return back()->with('success', 'Campaign deleted.');
    }
}
