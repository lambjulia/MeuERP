<?php

namespace Database\Seeders;

use App\Enums\FinancialStatus;
use App\Enums\PurchaseStatus;
use App\Enums\SaleStatus;
use App\Enums\StockMovementType;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $admin = Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'user']);

        // Company
        $company = Company::create([
            'name' => 'Empresa Demonstração LTDA',
            'document' => '12.345.678/0001-90',
            'email' => 'contato@empresademo.com.br',
            'phone' => '(11) 3456-7890',
        ]);

        // Admin user
        $user = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@meuerp.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
        ]);
        $user->assignRole($admin);

        // Categories
        $categories = collect([
            'Eletrônicos', 'Informática', 'Escritório', 'Limpeza', 'Alimentícios',
            'Ferramentas', 'Embalagens', 'Móveis',
        ])->map(fn ($name) => Category::create([
            'company_id' => $company->id,
            'name' => $name,
        ]));

        // Suppliers
        $suppliers = collect([
            ['name' => 'Tech Distribuidora', 'document' => '11.111.111/0001-01', 'email' => 'vendas@techdist.com.br', 'phone' => '(11) 1111-1111'],
            ['name' => 'Office Supply Brasil', 'document' => '22.222.222/0001-02', 'email' => 'contato@officesupply.com.br', 'phone' => '(11) 2222-2222'],
            ['name' => 'Atacadão Limpeza', 'document' => '33.333.333/0001-03', 'email' => 'vendas@atacadaolimpeza.com.br', 'phone' => '(11) 3333-3333'],
            ['name' => 'Mega Ferramentas', 'document' => '44.444.444/0001-04', 'email' => 'vendas@megaferramentas.com.br', 'phone' => '(11) 4444-4444'],
            ['name' => 'Embala Tudo', 'document' => '55.555.555/0001-05', 'email' => 'contato@embalatudo.com.br', 'phone' => '(11) 5555-5555'],
        ])->map(fn ($data) => Supplier::create([...$data, 'company_id' => $company->id]));

        // Customers
        $customers = collect([
            ['name' => 'João Silva', 'document' => '123.456.789-00', 'email' => 'joao@email.com', 'phone' => '(11) 91111-1111'],
            ['name' => 'Maria Oliveira', 'document' => '987.654.321-00', 'email' => 'maria@email.com', 'phone' => '(11) 92222-2222'],
            ['name' => 'Empresa ABC LTDA', 'document' => '66.666.666/0001-06', 'email' => 'compras@abc.com.br', 'phone' => '(11) 93333-3333'],
            ['name' => 'Carlos Santos', 'document' => '111.222.333-44', 'email' => 'carlos@email.com', 'phone' => '(11) 94444-4444'],
            ['name' => 'Ana Costa', 'document' => '555.666.777-88', 'email' => 'ana@email.com', 'phone' => '(11) 95555-5555'],
            ['name' => 'Pedro Mendes', 'document' => '999.888.777-66', 'email' => 'pedro@email.com', 'phone' => '(11) 96666-6666'],
        ])->map(fn ($data) => Customer::create([...$data, 'company_id' => $company->id]));

        // Products
        $products = collect([
            ['name' => 'Notebook Dell Inspiron', 'category' => 0, 'sku' => 'NB-001', 'cost_price' => 3500, 'sale_price' => 4500, 'stock' => 15, 'min' => 5],
            ['name' => 'Mouse Logitech MX Master', 'category' => 0, 'sku' => 'MS-001', 'cost_price' => 350, 'sale_price' => 499, 'stock' => 30, 'min' => 10],
            ['name' => 'Teclado Mecânico Redragon', 'category' => 0, 'sku' => 'TC-001', 'cost_price' => 200, 'sale_price' => 320, 'stock' => 25, 'min' => 8],
            ['name' => 'Monitor LG 27"', 'category' => 1, 'sku' => 'MN-001', 'cost_price' => 1200, 'sale_price' => 1600, 'stock' => 10, 'min' => 3],
            ['name' => 'Webcam HD 1080p', 'category' => 1, 'sku' => 'WC-001', 'cost_price' => 150, 'sale_price' => 250, 'stock' => 20, 'min' => 5],
            ['name' => 'Papel A4 500 folhas', 'category' => 2, 'sku' => 'PA-001', 'cost_price' => 18, 'sale_price' => 28, 'stock' => 100, 'min' => 20],
            ['name' => 'Caneta esferográfica cx 50', 'category' => 2, 'sku' => 'CN-001', 'cost_price' => 25, 'sale_price' => 45, 'stock' => 50, 'min' => 10],
            ['name' => 'Detergente 5L', 'category' => 3, 'sku' => 'DT-001', 'cost_price' => 12, 'sale_price' => 22, 'stock' => 40, 'min' => 10],
            ['name' => 'Álcool Gel 1L', 'category' => 3, 'sku' => 'AG-001', 'cost_price' => 8, 'sale_price' => 15, 'stock' => 3, 'min' => 15],
            ['name' => 'Caixa de papelão G', 'category' => 6, 'sku' => 'CX-001', 'cost_price' => 3, 'sale_price' => 6, 'stock' => 200, 'min' => 50],
            ['name' => 'Fita adesiva 50m', 'category' => 6, 'sku' => 'FT-001', 'cost_price' => 5, 'sale_price' => 10, 'stock' => 80, 'min' => 20],
            ['name' => 'Chave Phillips conjunto', 'category' => 5, 'sku' => 'CH-001', 'cost_price' => 35, 'sale_price' => 60, 'stock' => 15, 'min' => 5],
        ])->map(fn ($p) => Product::create([
            'company_id' => $company->id,
            'category_id' => $categories[$p['category']]->id,
            'name' => $p['name'],
            'sku' => $p['sku'],
            'cost_price' => $p['cost_price'],
            'sale_price' => $p['sale_price'],
            'stock_quantity' => $p['stock'],
            'minimum_stock' => $p['min'],
            'active' => true,
        ]));

        // Initial stock movements
        foreach ($products as $product) {
            StockMovement::create([
                'company_id' => $company->id,
                'product_id' => $product->id,
                'type' => StockMovementType::In->value,
                'quantity' => $product->stock_quantity,
                'balance_after' => $product->stock_quantity,
                'notes' => 'Estoque inicial',
            ]);
        }

        // Sample confirmed purchase
        $purchase = Purchase::create([
            'company_id' => $company->id,
            'supplier_id' => $suppliers[0]->id,
            'number' => 'PO-'.uniqid(),
            'issue_date' => now()->subDays(15)->toDateString(),
            'status' => PurchaseStatus::Confirmed->value,
            'total' => 0,
            'notes' => 'Compra de reposição',
        ]);

        $purchaseTotal = 0;
        foreach ([0, 1, 2] as $idx) {
            $qty = rand(5, 10);
            $price = $products[$idx]->cost_price;
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $products[$idx]->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => $qty * $price,
            ]);
            $purchaseTotal += $qty * $price;
        }
        $purchase->update(['total' => $purchaseTotal]);

        AccountPayable::create([
            'company_id' => $company->id,
            'supplier_id' => $suppliers[0]->id,
            'description' => "Compra #{$purchase->id}",
            'amount' => $purchaseTotal,
            'due_date' => now()->subDays(15)->addDays(30)->toDateString(),
            'status' => FinancialStatus::Pending->value,
        ]);

        // Sample draft purchase
        $draft = Purchase::create([
            'company_id' => $company->id,
            'supplier_id' => $suppliers[1]->id,
            'number' => 'PO-'.uniqid(),
            'issue_date' => now()->toDateString(),
            'status' => PurchaseStatus::Draft->value,
            'total' => 0,
        ]);
        $draftTotal = 0;
        foreach ([3, 4] as $idx) {
            $qty = rand(3, 8);
            $price = $products[$idx]->cost_price;
            PurchaseItem::create([
                'purchase_id' => $draft->id,
                'product_id' => $products[$idx]->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => $qty * $price,
            ]);
            $draftTotal += $qty * $price;
        }
        $draft->update(['total' => $draftTotal]);

        // Sample confirmed sale
        $sale = Sale::create([
            'company_id' => $company->id,
            'customer_id' => $customers[0]->id,
            'number' => 'SO-'.uniqid(),
            'issue_date' => now()->subDays(5)->toDateString(),
            'status' => SaleStatus::Confirmed->value,
            'total' => 0,
        ]);

        $saleTotal = 0;
        foreach ([0, 5, 6] as $idx) {
            $qty = rand(1, 5);
            $price = $products[$idx]->sale_price;
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $products[$idx]->id,
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => $qty * $price,
            ]);
            $saleTotal += $qty * $price;
        }
        $sale->update(['total' => $saleTotal]);

        AccountReceivable::create([
            'company_id' => $company->id,
            'customer_id' => $customers[0]->id,
            'description' => "Venda #{$sale->id}",
            'amount' => $saleTotal,
            'due_date' => now()->subDays(5)->addDays(30)->toDateString(),
            'status' => FinancialStatus::Pending->value,
        ]);

        // Extra accounts payable (paid & overdue)
        AccountPayable::create([
            'company_id' => $company->id,
            'supplier_id' => $suppliers[2]->id,
            'description' => 'Compra de material de limpeza',
            'amount' => 850.00,
            'due_date' => now()->subDays(30)->toDateString(),
            'paid_at' => now()->subDays(28)->toDateString(),
            'status' => FinancialStatus::Paid->value,
        ]);

        AccountPayable::create([
            'company_id' => $company->id,
            'supplier_id' => $suppliers[3]->id,
            'description' => 'Compra de ferramentas',
            'amount' => 1200.00,
            'due_date' => now()->subDays(5)->toDateString(),
            'status' => FinancialStatus::Overdue->value,
        ]);

        // Extra accounts receivable
        AccountReceivable::create([
            'company_id' => $company->id,
            'customer_id' => $customers[1]->id,
            'description' => 'Venda avulsa - serviços',
            'amount' => 2500.00,
            'due_date' => now()->addDays(15)->toDateString(),
            'status' => FinancialStatus::Pending->value,
        ]);

        AccountReceivable::create([
            'company_id' => $company->id,
            'customer_id' => $customers[2]->id,
            'description' => 'Venda equipamentos',
            'amount' => 8900.00,
            'due_date' => now()->subDays(10)->toDateString(),
            'received_at' => now()->subDays(8)->toDateString(),
            'status' => FinancialStatus::Paid->value,
        ]);
    }
}
