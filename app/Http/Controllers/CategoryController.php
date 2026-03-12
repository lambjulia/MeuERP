<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $categories = Category::where('company_id', $companyId)
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(CategoryRequest $request)
    {
        Category::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria removida com sucesso.');
    }
}
