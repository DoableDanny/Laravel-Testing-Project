<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index(): View {
        // $products = Product::all();
        $products = Product::paginate(10);
        return view('products.index', compact('products'));
    }

    public function create(): View {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse {
        Product::create($request->validated());
        return redirect()->route('products.index');
    }

    public function edit(Product $product): View {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse {
        $product->update($request->validated());
        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse {
        $product->delete();
        return redirect()->route('products.index');
    }
}
