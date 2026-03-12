<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $suppliers = Supplier::where('company_id', $companyId)
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->has('active'), fn ($q) => $q->where('active', $request->boolean('active')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => $request->only('search', 'active'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    public function store(SupplierRequest $request)
    {
        Supplier::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
        ]);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor criado com sucesso.');
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor atualizado com sucesso.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Fornecedor removido com sucesso.');
    }
}
