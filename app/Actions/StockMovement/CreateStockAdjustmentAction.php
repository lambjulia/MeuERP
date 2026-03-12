<?php

namespace App\Actions\StockMovement;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CreateStockAdjustmentAction
{
    public function execute(int $companyId, int $productId, StockMovementType $type, float $quantity, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($companyId, $productId, $type, $quantity, $notes) {
            $product = Product::findOrFail($productId);

            if ($type === StockMovementType::In) {
                $product->increment('stock_quantity', (int) $quantity);
            } elseif ($type === StockMovementType::Out) {
                if ($product->stock_quantity < $quantity) {
                    throw new \DomainException('Estoque insuficiente para essa saída.');
                }
                $product->decrement('stock_quantity', (int) $quantity);
            } else {
                // Adjustment: set absolute value
                $product->update(['stock_quantity' => (int) $quantity]);
            }

            return StockMovement::create([
                'company_id' => $companyId,
                'product_id' => $productId,
                'type' => $type,
                'reference_type' => null,
                'reference_id' => null,
                'quantity' => $quantity,
                'balance_after' => $product->fresh()->stock_quantity,
                'notes' => $notes,
            ]);
        });
    }
}
