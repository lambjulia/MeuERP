<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'description' => ['required', 'string', 'max:255'],
            'due_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
