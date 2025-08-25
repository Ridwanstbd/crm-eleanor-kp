<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $groups = CustomerGroup::withCount([
                'customers as customers_count' => function ($q) use ($search) {
                    $q->when($search, function ($qq) use ($search) {
                        $qq->where(function ($w) use ($search) {
                            $w->where('name', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%");
                        });
                    });
                }
            ])
            ->orderBy('created_at','desc')
            ->paginate(10)
            ->appends($request->query());


        return view('pages.Admin.Customer.index', compact('groups', 'search'));
    }

    public function showGroup(Request $request, CustomerGroup $group)
    {
        $prev = url()->previous();
        if ($prev && Str::startsWith($prev, route('customers.index', [], false))) {
            session(['customers_index_url' => $prev]);
        }

        $search = $request->string('search')->toString();
        $sort   = $request->input('sort', 'name');
        $dir    = $request->input('direction', 'asc');

        $allowed = ['name', 'phone'];
        if (!in_array($sort, $allowed)) $sort = 'name';
        if (!in_array($dir, ['asc', 'desc'])) $dir = 'asc';

        $customers = $group->customers()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->appends($request->query());

        $backUrl = session('customers_index_url') ?? route('customers.index');

        return view('pages.Admin.Customer.group_show', compact('group', 'customers', 'search', 'sort', 'dir', 'backUrl'));
    }

    public function create()
    {
        return view('pages.Admin.Customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:customers,name',
            'phone' => 'required|string|max:20|unique:customers,phone',
        ]);

        Customer::create($request->only('name', 'phone'));

        return $this->redirectToReturnOrBack($request)
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        abort(404);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:customers,name,' . $customer->id,
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
        ]);

        $customer->update($request->only('name', 'phone'));

        return $this->redirectToReturnOrBack($request)
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        $customer->delete();

        return $this->redirectToReturnOrBack($request)
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    private function redirectToReturnOrBack(Request $request)
    {
        $url = $request->input('return_url');

        if ($url && Str::startsWith($url, [url('/'), '/'])) {
            return redirect()->to($url);
        }

        $previous = url()->previous();
        if ($previous && Str::startsWith($previous, [url('/'), '/'])) {
            return redirect()->to($previous);
        }

        return redirect()->route('customers.index');
    }
}
