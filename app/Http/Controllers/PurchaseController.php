<?php

namespace App\Http\Controllers;

use App\Actions\Purchase\CancelPurchaseAction;
use App\Actions\Purchase\ConfirmPurchaseAction;
use App\Enums\PurchaseStatus;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $purchases = Purchase::where('company_id', $companyId)
            ->with('supplier:id,name')
            ->when($request->search, fn ($q, $search) => $q->where('number', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('issue_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
            'filters' => $request->only('search', 'status'),
            'statuses' => collect(PurchaseStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'suppliers' => Supplier::where('company_id', $companyId)->where('active', true)->select('id', 'name')->orderBy('name')->get(),
            'products' => Product::where('company_id', $companyId)->where('active', true)->select('id', 'name', 'cost_price')->orderBy('name')->get(),
        ]);
    }

    public function store(PurchaseRequest $request)
    {
        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'company_id' => $request->user()->company_id,
                'supplier_id' => $request->validated('supplier_id'),
                'number' => $request->validated('number'),
                'issue_date' => $request->validated('issue_date'),
                'notes' => $request->validated('notes'),
                'status' => PurchaseStatus::Draft,
                'total' => 0,
            ]);

            foreach ($request->validated('items') as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $purchase->recalculateTotal();
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Compra criada com sucesso.');
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        if ($purchase->status !== PurchaseStatus::Draft) {
            return redirect()->route('purchases.index')
                ->with('error', 'Apenas compras em rascunho podem ser editadas.');
        }

        DB::transaction(function () use ($request, $purchase) {
            $purchase->update([
                'supplier_id' => $request->validated('supplier_id'),
                'number' => $request->validated('number'),
                'issue_date' => $request->validated('issue_date'),
                'notes' => $request->validated('notes'),
            ]);

            $purchase->items()->delete();

            foreach ($request->validated('items') as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            $purchase->recalculateTotal();
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Compra atualizada com sucesso.');
    }

    public function confirm(Purchase $purchase, ConfirmPurchaseAction $action)
    {
        try {
            $action->execute($purchase);
            return redirect()->route('purchases.index')
                ->with('success', 'Compra confirmada com sucesso.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Purchase $purchase, CancelPurchaseAction $action)
    {
        try {
            $action->execute($purchase);
            return redirect()->route('purchases.index')
                ->with('success', 'Compra cancelada com sucesso.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status !== PurchaseStatus::Draft) {
            return redirect()->back()
                ->with('error', 'Apenas compras em rascunho podem ser excluídas.');
        }

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Compra removida com sucesso.');
    }
}
