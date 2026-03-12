<?php

namespace App\Actions\Sale;

use App\Enums\FinancialStatus;
use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Models\AccountReceivable;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ConfirmSaleAction
{
    public function execute(Sale $sale): void
    {
        if ($sale->status !== SaleStatus::Draft) {
            throw new \DomainException('Apenas vendas em rascunho podem ser confirmadas.');
        }

        // Validate stock availability
        foreach ($sale->items as $item) {
            $product = $item->product;
            if ($product->stock_quantity < $item->quantity) {
                throw new \DomainException(
                    "Estoque insuficiente para o produto '{$product->name}'. Disponível: {$product->stock_quantity}, Solicitado: {$item->quantity}"
                );
            }
        }

        DB::transaction(function () use ($sale) {
            $sale->update(['status' => SaleStatus::Confirmed]);

            foreach ($sale->items as $item) {
                $product = $item->product;
                $product->decrement('stock_quantity', (int) $item->quantity);

                StockMovement::create([
                    'company_id' => $sale->company_id,
                    'product_id' => $item->product_id,
                    'type' => StockMovementType::Out,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'quantity' => $item->quantity,
                    'balance_after' => $product->fresh()->stock_quantity,
                    'notes' => "Saída via venda #{$sale->number}",
                ]);
            }

            AccountReceivable::create([
                'company_id' => $sale->company_id,
                'customer_id' => $sale->customer_id,
                'description' => "Venda #{$sale->number}",
                'due_date' => $sale->issue_date->addDays(30),
                'amount' => $sale->total,
                'status' => FinancialStatus::Pending,
            ]);
        });
    }
}
