<?php

namespace Tests\Feature;

use App\Actions\Sale\ConfirmSaleAction;
use App\Actions\Sale\CancelSaleAction;
use App\Enums\FinancialStatus;
use App\Enums\SaleStatus;
use App\Models\AccountReceivable;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Customer $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create(['name' => 'Test Co', 'cnpj' => '00.000.000/0001-00']);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        $this->customer = Customer::create(['company_id' => $this->company->id, 'name' => 'Customer', 'cpf_cnpj' => '123.456.789-00']);
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'cost_price' => 100,
            'sale_price' => 150,
            'stock_quantity' => 20,
            'minimum_stock' => 5,
            'active' => true,
        ]);
    }

    private function createSale(string $status = 'draft', int $qty = 3, float $price = 150): Sale
    {
        $sale = Sale::create([
            'company_id' => $this->company->id,
            'customer_id' => $this->customer->id,
            'date' => now()->toDateString(),
            'status' => $status,
            'total' => $qty * $price,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => $qty,
            'unit_price' => $price,
            'subtotal' => $qty * $price,
        ]);

        return $sale;
    }

    public function test_confirm_sale_decrements_stock(): void
    {
        $sale = $this->createSale('draft', 3);
        $initialStock = $this->product->stock_quantity;

        $action = new ConfirmSaleAction();
        $action->execute($sale);

        $this->product->refresh();
        $this->assertEquals($initialStock - 3, $this->product->stock_quantity);
    }

    public function test_confirm_sale_changes_status_to_confirmed(): void
    {
        $sale = $this->createSale('draft');

        (new ConfirmSaleAction())->execute($sale);

        $sale->refresh();
        $this->assertEquals(SaleStatus::Confirmed, $sale->status);
    }

    public function test_confirm_sale_creates_stock_movements(): void
    {
        $sale = $this->createSale('draft', 3);

        (new ConfirmSaleAction())->execute($sale);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 3,
        ]);
    }

    public function test_confirm_sale_creates_account_receivable(): void
    {
        $sale = $this->createSale('draft', 3, 150);

        (new ConfirmSaleAction())->execute($sale);

        $this->assertDatabaseHas('accounts_receivable', [
            'customer_id' => $this->customer->id,
            'amount' => 450,
            'status' => FinancialStatus::Pending->value,
        ]);
    }

    public function test_confirm_sale_fails_with_insufficient_stock(): void
    {
        $sale = $this->createSale('draft', 999, 150);

        $action = new ConfirmSaleAction();

        $this->expectException(\DomainException::class);
        $action->execute($sale);
    }

    public function test_confirm_sale_fails_if_not_draft(): void
    {
        $sale = $this->createSale('confirmed');

        $this->expectException(\DomainException::class);
        (new ConfirmSaleAction())->execute($sale);
    }

    public function test_cancel_sale_restores_stock(): void
    {
        $sale = $this->createSale('draft', 3);
        (new ConfirmSaleAction())->execute($sale);

        $this->product->refresh();
        $stockAfterConfirm = $this->product->stock_quantity;

        (new CancelSaleAction())->execute($sale->fresh());

        $this->product->refresh();
        $this->assertEquals($stockAfterConfirm + 3, $this->product->stock_quantity);
    }

    public function test_cancel_sale_changes_status_to_cancelled(): void
    {
        $sale = $this->createSale('draft');
        (new ConfirmSaleAction())->execute($sale);

        (new CancelSaleAction())->execute($sale->fresh());

        $sale->refresh();
        $this->assertEquals(SaleStatus::Cancelled, $sale->status);
    }
}
