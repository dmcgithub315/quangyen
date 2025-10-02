<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}


