<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index', ['customers' => Customer::all()]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers',
        ]);

        Customer::create($request->only('name', 'phone'));

        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers,phone,' . $customer->id,
        ]);

        $customer->update($request->only('name', 'phone'));

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }

    public function attachGroup(Request $request, Customer $customer)
    {
        $request->validate(['group_id' => 'required|exists:customer_groups,id']);
        $customer->groups()->attach($request->group_id);
        return back()->with('success', 'Group attached.');
    }

    public function detachGroup(Request $request, Customer $customer)
    {
        $customer->groups()->detach($request->group_id);
        return back()->with('success', 'Group detached.');
    }

    public function attachProduct(Request $request, Customer $customer)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'last_purchase_quantity' => 'required|integer|min:1',
        ]);
        $customer->purchases()->attach($request->product_id, [
            'last_purchase_quantity' => $request->last_purchase_quantity,
        ]);
        return back()->with('success', 'Product attached.');
    }

    public function detachProduct(Request $request, Customer $customer)
    {
        $customer->purchases()->detach($request->product_id);
        return back()->with('success', 'Product detached.');
    }
}
