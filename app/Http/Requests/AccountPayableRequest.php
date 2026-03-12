<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountPayableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'description' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
