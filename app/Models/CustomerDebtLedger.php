<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDebtLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bill_id',
        'repayment_id',
        'type',
        'amount',
        'balance_after',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function repayment(): BelongsTo
    {
        return $this->belongsTo(RepaymentHistory::class, 'repayment_id');
    }
}


