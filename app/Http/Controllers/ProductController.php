<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
class ProductController extends Controller
{

    public function index(Request $request)
    {
        $products = Product::all();

        if ($products->has('sort')) {
            $products->orderBy($request->get('sort'), $request->get('direction', 'asc'));
        }
        return view('pages.Admin.Product.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products',
            'default_estimation_days_per_unit' => 'required|integer|min:1',
        ]);

        Product::create($request->only('name', 'default_estimation_days_per_unit'));

        return redirect()->route('products.index')->with('success', 'Product created.');
    }


    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|unique:products,name,' . $product->id,
            'default_estimation_days_per_unit' => 'required|integer|min:1',
        ]);

        $product->update($request->only('name', 'default_estimation_days_per_unit'));

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
