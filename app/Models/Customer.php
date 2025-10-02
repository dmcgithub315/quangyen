<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'total_debt',
    ];

    protected $casts = [
        'total_debt' => 'decimal:2',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(RepaymentHistory::class);
    }
    
}


