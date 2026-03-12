<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $products = Product::where('company_id', $companyId)
            ->with('category:id,name')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->category_id, fn ($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when($request->has('active'), fn ($q) => $q->where('active', $request->boolean('active')))
            ->when($request->low_stock, fn ($q) => $q->whereColumn('stock_quantity', '<=', 'minimum_stock'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only('search', 'category_id', 'active', 'low_stock'),
            'categories' => Category::where('company_id', $companyId)
                ->where('active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(Request $request)
    {
        $companyId = $request->user()->company_id;

        return Inertia::render('Products/Create', [
            'categories' => Category::where('company_id', $companyId)
                ->where('active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        Product::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso.');
    }

    public function show(Product $product)
    {
        $product->load('category:id,name', 'stockMovements');

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Request $request, Product $product)
    {
        $companyId = $request->user()->company_id;

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => Category::where('company_id', $companyId)
                ->where('active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto removido com sucesso.');
    }
}
