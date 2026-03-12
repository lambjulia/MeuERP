<?php

namespace App\Http\Controllers;

use App\Enums\FinancialStatus;
use App\Enums\PurchaseStatus;
use App\Enums\SaleStatus;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_products' => Product::where('company_id', $companyId)->count(),
                'total_customers' => Customer::where('company_id', $companyId)->count(),
                'total_suppliers' => Supplier::where('company_id', $companyId)->count(),
                'low_stock' => Product::where('company_id', $companyId)
                    ->whereColumn('stock_quantity', '<=', 'minimum_stock')
                    ->count(),
                'total_payable' => AccountPayable::where('company_id', $companyId)
                    ->where('status', FinancialStatus::Pending)
                    ->sum('amount'),
                'total_receivable' => AccountReceivable::where('company_id', $companyId)
                    ->where('status', FinancialStatus::Pending)
                    ->sum('amount'),
                'sales_month' => Sale::where('company_id', $companyId)
                    ->where('status', SaleStatus::Confirmed)
                    ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
                    ->sum('total'),
                'purchases_month' => Purchase::where('company_id', $companyId)
                    ->where('status', PurchaseStatus::Confirmed)
                    ->whereBetween('issue_date', [$startOfMonth, $endOfMonth])
                    ->sum('total'),
            ],
            'low_stock_products' => Product::where('company_id', $companyId)
                ->whereColumn('stock_quantity', '<=', 'minimum_stock')
                ->select('id', 'name', 'stock_quantity', 'minimum_stock')
                ->limit(10)
                ->get(),
        ]);
    }
}
