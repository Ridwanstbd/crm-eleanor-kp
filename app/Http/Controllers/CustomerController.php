<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * INDEX: daftar grup + badge jumlah pelanggan (terfilter oleh search) + pagination grup.
     */
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
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        $ungroupedCount = Customer::doesntHave('groups')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->count();

        return view('pages.Admin.Customer.index', compact('groups', 'ungroupedCount', 'search'));
    }

    /**
     * DETAIL GRUP: tabel pelanggan dalam satu grup + search + sort + pagination.
     * Simpan URL index terakhir ke session agar tombol Kembali bisa balik ke state yang sama.
     */
    public function showGroup(Request $request, CustomerGroup $group)
    {
        // Jika datang dari index, simpan URL index terakhir ke session
        $prev = url()->previous();
        if ($prev && Str::startsWith($prev, route('customers.index', [], false))) {
            session(['customers_index_url' => $prev]);
        }

        $search = $request->string('search')->toString();
        $sort   = $request->input('sort', 'name');      // name | phone
        $dir    = $request->input('direction', 'asc');  // asc | desc

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

        // URL kembali ke index (dengan query & page yang sama) atau fallback index
        $backUrl = session('customers_index_url') ?? route('customers.index');

        return view('pages.Admin.Customer.group_show', compact('group', 'customers', 'search', 'sort', 'dir', 'backUrl'));
    }

    /**
     * DETAIL TANPA GRUP: daftar pelanggan tanpa grup + search + sort + pagination.
     * Simpan URL index terakhir ke session agar tombol Kembali bisa balik ke state yang sama.
     */
    public function showUngrouped(Request $request)
    {
        // Jika datang dari index, simpan URL index terakhir ke session
        $prev = url()->previous();
        if ($prev && Str::startsWith($prev, route('customers.index', [], false))) {
            session(['customers_index_url' => $prev]);
        }

        $search = $request->string('search')->toString();
        $sort   = $request->input('sort', 'name');      // name | phone
        $dir    = $request->input('direction', 'asc');  // asc | desc

        $allowed = ['name','phone'];
        if (!in_array($sort, $allowed)) $sort = 'name';
        if (!in_array($dir, ['asc','desc'])) $dir = 'asc';

        $customers = Customer::doesntHave('groups')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name','like',"%{$search}%")
                      ->orWhere('phone','like',"%{$search}%");
                });
            })
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->appends($request->query());

        // URL kembali ke index (dengan query & page yang sama) atau fallback index
        $backUrl = session('customers_index_url') ?? route('customers.index');

        return view('pages.Admin.Customer.ungrouped_show', compact('customers','search','sort','dir','backUrl'));
    }

    /**
     * CREATE (opsional; jika form via modal di index/detail, boleh abaikan blade terpisah).
     */
    public function create()
    {
        return view('pages.Admin.Customer.create');
    }

    /**
     * STORE: hormati return_url agar kembali ke halaman pemanggil (detail grup/ungroup).
     */
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

    /**
     * SHOW tidak dipakai pada desain ini.
     */
    public function show(Customer $customer)
    {
        abort(404);
    }

    /**
     * UPDATE: tetap di halaman detail yang memanggil (group/ungroup) via return_url/back.
     */
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

    /**
     * DESTROY: tetap di halaman detail yang memanggil (group/ungroup) via return_url/back.
     */
    public function destroy(Request $request, Customer $customer)
    {
        $customer->delete();

        return $this->redirectToReturnOrBack($request)
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Helper: redirect ke return_url (jika valid & internal), atau ke previous(), atau ke index.
     */
    private function redirectToReturnOrBack(Request $request)
    {
        $url = $request->input('return_url');

        // Guard sederhana untuk mencegah open redirect
        if ($url && Str::startsWith($url, [url('/'), '/'])) {
            return redirect()->to($url);
        }

        // Jika tidak ada return_url, gunakan previous (Referer) bila valid
        $previous = url()->previous();
        if ($previous && Str::startsWith($previous, [url('/'), '/'])) {
            return redirect()->to($previous);
        }

        // Fallback terakhir
        return redirect()->route('customers.index');
    }
}
