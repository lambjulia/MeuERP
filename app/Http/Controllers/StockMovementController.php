<?php

namespace App\Http\Controllers;

use App\Actions\StockMovement\CreateStockAdjustmentAction;
use App\Enums\StockMovementType;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $movements = StockMovement::where('company_id', $companyId)
            ->with('product:id,name')
            ->when($request->product_id, fn ($q, $productId) => $q->where('product_id', $productId))
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Stock/Index', [
            'movements' => $movements,
            'filters' => $request->only('product_id', 'type'),
            'products' => Product::where('company_id', $companyId)
                ->where('active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
            'types' => collect(StockMovementType::cases())->map(fn ($t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
        ]);
    }

    public function store(StockAdjustmentRequest $request, CreateStockAdjustmentAction $action)
    {
        $action->execute(
            $request->user()->company_id,
            $request->validated('product_id'),
            StockMovementType::from($request->validated('type')),
            $request->validated('quantity'),
            $request->validated('notes'),
        );

        return redirect()->route('stock.index')
            ->with('success', 'Movimentação registrada com sucesso.');
    }
}
