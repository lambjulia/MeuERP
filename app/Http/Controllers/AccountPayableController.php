<?php

namespace App\Http\Controllers;

use App\Enums\FinancialStatus;
use App\Http\Requests\AccountPayableRequest;
use App\Models\AccountPayable;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountPayableController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $accounts = AccountPayable::where('company_id', $companyId)
            ->with('supplier:id,name')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('description', 'like', "%{$search}%"))
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('AccountsPayable/Index', [
            'accounts' => $accounts,
            'filters' => $request->only('search', 'status'),
            'statuses' => collect(FinancialStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'suppliers' => Supplier::where('company_id', $companyId)->where('active', true)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(AccountPayableRequest $request)
    {
        AccountPayable::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
            'status' => FinancialStatus::Pending,
        ]);

        return redirect()->route('accounts-payable.index')
            ->with('success', 'Conta a pagar criada com sucesso.');
    }

    public function update(AccountPayableRequest $request, AccountPayable $accounts_payable)
    {
        $accounts_payable->update($request->validated());

        return redirect()->route('accounts-payable.index')
            ->with('success', 'Conta a pagar atualizada com sucesso.');
    }

    public function markAsPaid(AccountPayable $accounts_payable)
    {
        $accounts_payable->update([
            'status' => FinancialStatus::Paid,
            'paid_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Conta marcada como paga.');
    }

    public function destroy(AccountPayable $accounts_payable)
    {
        $accounts_payable->delete();

        return redirect()->route('accounts-payable.index')
            ->with('success', 'Conta a pagar removida com sucesso.');
    }
}
