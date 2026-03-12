<?php

namespace App\Http\Requests;

use App\Enums\StockMovementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in([StockMovementType::In->value, StockMovementType::Out->value, StockMovementType::Adjustment->value])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
