<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierRepaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'amount',
        'method',
        'date',
        'remaining_debt',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_debt' => 'decimal:2',
        'date' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}


