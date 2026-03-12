<?php

namespace App\Actions\Sale;

use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CancelSaleAction
{
    public function execute(Sale $sale): void
    {
        if ($sale->status !== SaleStatus::Confirmed) {
            throw new \DomainException('Apenas vendas confirmadas podem ser canceladas.');
        }

        DB::transaction(function () use ($sale) {
            $sale->update(['status' => SaleStatus::Cancelled]);

            foreach ($sale->items as $item) {
                $product = $item->product;
                $product->increment('stock_quantity', (int) $item->quantity);

                StockMovement::create([
                    'company_id' => $sale->company_id,
                    'product_id' => $item->product_id,
                    'type' => StockMovementType::In,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'quantity' => $item->quantity,
                    'balance_after' => $product->fresh()->stock_quantity,
                    'notes' => "Estorno de venda #{$sale->number}",
                ]);
            }
        });
    }
}
