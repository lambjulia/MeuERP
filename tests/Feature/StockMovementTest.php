<?php

namespace Tests\Feature;

use App\Actions\StockMovement\CreateStockAdjustmentAction;
use App\Enums\StockMovementType;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create(['name' => 'Test Co', 'cnpj' => '00.000.000/0001-00']);
        User::factory()->create(['company_id' => $this->company->id]);
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'cost_price' => 10,
            'sale_price' => 20,
            'stock_quantity' => 50,
            'minimum_stock' => 10,
            'active' => true,
        ]);
    }

    public function test_stock_adjustment_in_adds_stock(): void
    {
        $action = new CreateStockAdjustmentAction();
        $action->execute([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'type' => StockMovementType::In->value,
            'quantity' => 10,
            'notes' => 'Entrada manual',
        ]);

        $this->product->refresh();
        $this->assertEquals(60, $this->product->stock_quantity);
    }

    public function test_stock_adjustment_out_removes_stock(): void
    {
        $action = new CreateStockAdjustmentAction();
        $action->execute([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'type' => StockMovementType::Out->value,
            'quantity' => 5,
            'notes' => 'Saída manual',
        ]);

        $this->product->refresh();
        $this->assertEquals(45, $this->product->stock_quantity);
    }

    public function test_stock_adjustment_sets_absolute_value(): void
    {
        $action = new CreateStockAdjustmentAction();
        $action->execute([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'type' => StockMovementType::Adjustment->value,
            'quantity' => 30,
            'notes' => 'Ajuste de inventário',
        ]);

        $this->product->refresh();
        $this->assertEquals(30, $this->product->stock_quantity);
    }

    public function test_stock_movement_records_balance_after(): void
    {
        $action = new CreateStockAdjustmentAction();
        $action->execute([
            'company_id' => $this->company->id,
            'product_id' => $this->product->id,
            'type' => StockMovementType::In->value,
            'quantity' => 15,
            'notes' => 'Test balance',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 15,
            'balance_after' => 65,
        ]);
    }
}
