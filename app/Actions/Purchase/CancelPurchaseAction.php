<?php

namespace App\Actions\Purchase;

use App\Enums\PurchaseStatus;
use App\Enums\StockMovementType;
use App\Models\Purchase;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CancelPurchaseAction
{
    public function execute(Purchase $purchase): void
    {
        if ($purchase->status !== PurchaseStatus::Confirmed) {
            throw new \DomainException('Apenas compras confirmadas podem ser canceladas.');
        }

        DB::transaction(function () use ($purchase) {
            $purchase->update(['status' => PurchaseStatus::Cancelled]);

            foreach ($purchase->items as $item) {
                $product = $item->product;
                $product->decrement('stock_quantity', (int) $item->quantity);

                StockMovement::create([
                    'company_id' => $purchase->company_id,
                    'product_id' => $item->product_id,
                    'type' => StockMovementType::Out,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'quantity' => $item->quantity,
                    'balance_after' => $product->fresh()->stock_quantity,
                    'notes' => "Estorno de compra #{$purchase->number}",
                ]);
            }
        });
    }
}
