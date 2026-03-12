<?php

namespace App\Actions\Purchase;

use App\Enums\FinancialStatus;
use App\Enums\PurchaseStatus;
use App\Enums\StockMovementType;
use App\Models\AccountPayable;
use App\Models\Purchase;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ConfirmPurchaseAction
{
    public function execute(Purchase $purchase): void
    {
        if ($purchase->status !== PurchaseStatus::Draft) {
            throw new \DomainException('Apenas compras em rascunho podem ser confirmadas.');
        }

        DB::transaction(function () use ($purchase) {
            $purchase->update(['status' => PurchaseStatus::Confirmed]);

            foreach ($purchase->items as $item) {
                $product = $item->product;
                $product->increment('stock_quantity', (int) $item->quantity);

                StockMovement::create([
                    'company_id' => $purchase->company_id,
                    'product_id' => $item->product_id,
                    'type' => StockMovementType::In,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'quantity' => $item->quantity,
                    'balance_after' => $product->fresh()->stock_quantity,
                    'notes' => "Entrada via compra #{$purchase->number}",
                ]);
            }

            AccountPayable::create([
                'company_id' => $purchase->company_id,
                'supplier_id' => $purchase->supplier_id,
                'description' => "Compra #{$purchase->number}",
                'due_date' => $purchase->issue_date->addDays(30),
                'amount' => $purchase->total,
                'status' => FinancialStatus::Pending,
            ]);
        });
    }
}
