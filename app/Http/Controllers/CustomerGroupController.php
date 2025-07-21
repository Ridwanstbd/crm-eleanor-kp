<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function index()
    {
        return view('customer_groups.index', ['groups' => CustomerGroup::all()]);
    }

    public function create()
    {
        return view('customer_groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:customer_groups',
        ]);

        CustomerGroup::create($request->only('name'));

        return redirect()->route('customer-groups.index')->with('success', 'Group created.');
    }

    public function edit(CustomerGroup $customerGroup)
    {
        return view('customer_groups.edit', compact('customerGroup'));
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $request->validate([
            'name' => 'required|unique:customer_groups,name,' . $customerGroup->id,
        ]);

        $customerGroup->update($request->only('name'));

        return redirect()->route('customer-groups.index')->with('success', 'Group updated.');
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        $customerGroup->delete();
        return redirect()->route('customer-groups.index')->with('success', 'Group deleted.');
    }

    public function attachCustomer(Request $request, CustomerGroup $customerGroup)
    {
        $request->validate(['customer_id' => 'required|exists:customers,id']);
        $customerGroup->customers()->attach($request->customer_id);
        return back()->with('success', 'Customer added to group.');
    }

    public function detachCustomer(Request $request, CustomerGroup $customerGroup)
    {
        $customerGroup->customers()->detach($request->customer_id);
        return back()->with('success', 'Customer removed from group.');
    }
}
