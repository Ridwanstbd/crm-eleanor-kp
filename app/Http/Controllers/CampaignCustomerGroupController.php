<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CustomerGroup;

class CampaignCustomerGroupController extends Controller
{
    public function index()
    {
        return view('pivot.campaign_groups.index', [
            'campaigns' => Campaign::with('customerGroups')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'group_id' => 'required|exists:customer_groups,id',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);
        $campaign->customerGroups()->syncWithoutDetaching([$request->group_id]);

        return back()->with('success', 'Group linked to campaign.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required',
            'group_id' => 'required',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);
        $campaign->customerGroups()->detach($request->group_id);

        return back()->with('success', 'Group unlinked from campaign.');
    }
}
