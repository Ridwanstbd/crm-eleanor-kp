<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;

class CustomerCustomerGroupController extends Controller
{
    public function index()
    {
        return view('pivot.customer_groups.index', [
            'groups' => CustomerGroup::with('customers')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'group_id' => 'required|exists:customer_groups,id',
        ]);

        $group = CustomerGroup::findOrFail($request->group_id);
        $group->customers()->syncWithoutDetaching([$request->customer_id]);

        return back()->with('success', 'Customer added to group.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'group_id' => 'required',
        ]);

        $group = CustomerGroup::findOrFail($request->group_id);
        $group->customers()->detach($request->customer_id);

        return back()->with('success', 'Customer removed from group.');
    }
}
