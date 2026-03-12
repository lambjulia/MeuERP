<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'company_id',
        'supplier_id',
        'number',
        'issue_date',
        'total',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'total' => 'decimal:2',
            'status' => PurchaseStatus::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->update(['total' => $this->items()->sum('total')]);
    }
}
