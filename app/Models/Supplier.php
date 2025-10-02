<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
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

    public function imports(): HasMany
    {
        return $this->hasMany(ImportHistory::class);
    }

    public function supplierRepayments(): HasMany
    {
        return $this->hasMany(SupplierRepaymentHistory::class);
    }
}


