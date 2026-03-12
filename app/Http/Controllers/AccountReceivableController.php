<?php

namespace App\Http\Controllers;

use App\Enums\FinancialStatus;
use App\Http\Requests\AccountReceivableRequest;
use App\Models\AccountReceivable;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountReceivableController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $accounts = AccountReceivable::where('company_id', $companyId)
            ->with('customer:id,name')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('description', 'like', "%{$search}%"))
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('AccountsReceivable/Index', [
            'accounts' => $accounts,
            'filters' => $request->only('search', 'status'),
            'statuses' => collect(FinancialStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    public function create(Request $request)
    {
        $companyId = $request->user()->company_id;

        return Inertia::render('AccountsReceivable/Create', [
            'customers' => Customer::where('company_id', $companyId)->where('active', true)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(AccountReceivableRequest $request)
    {
        AccountReceivable::create([
            ...$request->validated(),
            'company_id' => $request->user()->company_id,
            'status' => FinancialStatus::Pending,
        ]);

        return redirect()->route('accounts-receivable.index')
            ->with('success', 'Conta a receber criada com sucesso.');
    }

    public function edit(Request $request, AccountReceivable $accounts_receivable)
    {
        $companyId = $request->user()->company_id;

        return Inertia::render('AccountsReceivable/Edit', [
            'account' => $accounts_receivable,
            'customers' => Customer::where('company_id', $companyId)->where('active', true)->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(AccountReceivableRequest $request, AccountReceivable $accounts_receivable)
    {
        $accounts_receivable->update($request->validated());

        return redirect()->route('accounts-receivable.index')
            ->with('success', 'Conta a receber atualizada com sucesso.');
    }

    public function markAsReceived(AccountReceivable $accounts_receivable)
    {
        $accounts_receivable->update([
            'status' => FinancialStatus::Paid,
            'received_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Conta marcada como recebida.');
    }

    public function destroy(AccountReceivable $accounts_receivable)
    {
        $accounts_receivable->delete();

        return redirect()->route('accounts-receivable.index')
            ->with('success', 'Conta a receber removida com sucesso.');
    }
}
