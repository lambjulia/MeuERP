<?php

namespace Tests\Feature;

use App\Actions\Purchase\ConfirmPurchaseAction;
use App\Actions\Purchase\CancelPurchaseAction;
use App\Enums\FinancialStatus;
use App\Enums\PurchaseStatus;
use App\Models\AccountPayable;
use App\Models\Company;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Supplier $supplier;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create(['name' => 'Test Co', 'cnpj' => '00.000.000/0001-00']);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->supplier = Supplier::create(['company_id' => $this->company->id, 'name' => 'Supplier', 'cnpj' => '11.111.111/0001-11']);
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'cost_price' => 100,
            'sale_price' => 150,
            'stock_quantity' => 10,
            'minimum_stock' => 5,
            'active' => true,
        ]);
    }

    private function createPurchase(string $status = 'draft', int $qty = 5, float $price = 100): Purchase
    {
        $purchase = Purchase::create([
            'company_id' => $this->company->id,
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'status' => $status,
            'total' => $qty * $price,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product->id,
            'quantity' => $qty,
            'unit_price' => $price,
            'subtotal' => $qty * $price,
        ]);

        return $purchase;
    }

    public function test_confirm_purchase_increments_stock(): void
    {
        $purchase = $this->createPurchase('draft', 5, 100);
        $initialStock = $this->product->stock_quantity;

        $action = new ConfirmPurchaseAction();
        $action->execute($purchase);

        $this->product->refresh();
        $this->assertEquals($initialStock + 5, $this->product->stock_quantity);
    }

    public function test_confirm_purchase_changes_status_to_confirmed(): void
    {
        $purchase = $this->createPurchase('draft');

        $action = new ConfirmPurchaseAction();
        $action->execute($purchase);

        $purchase->refresh();
        $this->assertEquals(PurchaseStatus::Confirmed, $purchase->status);
    }

    public function test_confirm_purchase_creates_stock_movements(): void
    {
        $purchase = $this->createPurchase('draft', 5);

        $action = new ConfirmPurchaseAction();
        $action->execute($purchase);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 5,
        ]);
    }

    public function test_confirm_purchase_creates_account_payable(): void
    {
        $purchase = $this->createPurchase('draft', 5, 100);

        $action = new ConfirmPurchaseAction();
        $action->execute($purchase);

        $this->assertDatabaseHas('accounts_payable', [
            'supplier_id' => $this->supplier->id,
            'amount' => 500,
            'status' => FinancialStatus::Pending->value,
        ]);
    }

    public function test_confirm_purchase_fails_if_not_draft(): void
    {
        $purchase = $this->createPurchase('confirmed');

        $action = new ConfirmPurchaseAction();

        $this->expectException(\DomainException::class);
        $action->execute($purchase);
    }

    public function test_cancel_purchase_decrements_stock(): void
    {
        $purchase = $this->createPurchase('draft', 5);
        $action = new ConfirmPurchaseAction();
        $action->execute($purchase);

        $this->product->refresh();
        $stockAfterConfirm = $this->product->stock_quantity;

        $cancelAction = new CancelPurchaseAction();
        $cancelAction->execute($purchase->fresh());

        $this->product->refresh();
        $this->assertEquals($stockAfterConfirm - 5, $this->product->stock_quantity);
    }

    public function test_cancel_purchase_changes_status_to_cancelled(): void
    {
        $purchase = $this->createPurchase('draft');
        (new ConfirmPurchaseAction())->execute($purchase);

        (new CancelPurchaseAction())->execute($purchase->fresh());

        $purchase->refresh();
        $this->assertEquals(PurchaseStatus::Cancelled, $purchase->status);
    }
}
