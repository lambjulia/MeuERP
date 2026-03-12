<?php

namespace App\Http\Controllers;

use App\Actions\Sale\CancelSaleAction;
use App\Actions\Sale\ConfirmSaleAction;
use App\Enums\SaleStatus;
use App\Http\Requests\SaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $sales = Sale::where('company_id', $companyId)
            ->with('customer:id,name')
            ->when($request->search, fn ($q, $search) => $q->where('number', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('issue_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'filters' => $request->only('search', 'status'),
            'statuses' => collect(SaleStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'customers' => Customer::where('company_id', $companyId)->where('active', true)->select('id', 'name')->orderBy('name')->get(),
            'products' => Product::where('company_id', $companyId)->where('active', true)->select('id', 'name', 'sale_price', 'stock_quantity')->orderBy('name')->get(),
        ]);
    }

    public function store(SaleRequest $request)
    {
        DB::transaction(function () use ($request) {
            $sale = Sale::create([
                'company_id' => $request->user()->company_id,
                'customer_id' => $request->validated('customer_id'),
                'number' => $request->validated('number'),
                'issue_date' => $request->validated('issue_date'),
                'notes' => $request->validated('notes'),
                'status' => SaleStatus::Draft,
                'total' => 0,
            ]);

            foreach ($request->validated('items') as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $sale->recalculateTotal();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Venda criada com sucesso.');
    }

    public function update(SaleRequest $request, Sale $sale)
    {
        if ($sale->status !== SaleStatus::Draft) {
            return redirect()->route('sales.index')
                ->with('error', 'Apenas vendas em rascunho podem ser editadas.');
        }

        DB::transaction(function () use ($request, $sale) {
            $sale->update([
                'customer_id' => $request->validated('customer_id'),
                'number' => $request->validated('number'),
                'issue_date' => $request->validated('issue_date'),
                'notes' => $request->validated('notes'),
            ]);

            $sale->items()->delete();

            foreach ($request->validated('items') as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $sale->recalculateTotal();
        });

        return redirect()->route('sales.index')
            ->with('success', 'Venda atualizada com sucesso.');
    }

    public function confirm(Sale $sale, ConfirmSaleAction $action)
    {
        try {
            $action->execute($sale);
            return redirect()->route('sales.index')
                ->with('success', 'Venda confirmada com sucesso.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Sale $sale, CancelSaleAction $action)
    {
        try {
            $action->execute($sale);
            return redirect()->route('sales.index')
                ->with('success', 'Venda cancelada com sucesso.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        if ($sale->status !== SaleStatus::Draft) {
            return redirect()->back()
                ->with('error', 'Apenas vendas em rascunho podem ser excluídas.');
        }

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Venda removida com sucesso.');
    }
}
