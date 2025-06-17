<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'price_unit',
        'stock_quantity',
        'stock_status',
        'origin',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the user who created the product.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the product.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the product information.
     */
    public function productInfo(): HasMany
    {
        return $this->hasMany(ProductInfo::class)->ordered();
    }

    /**
     * Get formatted price with unit.
     */
    public function getFormattedPriceAttribute()
    {
        if (!$this->price) return null;
        
        $price = number_format($this->price, 0, ',', '.');
        $unit = $this->price_unit ?: 'VNĐ';
        
        return "{$price} {$unit}";
    }

    /**
     * Get stock status in Vietnamese.
     */
    public function getStockStatusViAttribute()
    {
        return match($this->stock_status) {
            'in_stock' => 'Còn hàng',
            'out_of_stock' => 'Hết hàng',
            'on_backorder' => 'Đặt trước',
            default => 'Không xác định'
        };
    }

    /**
     * Check if product is available for purchase.
     */
    public function getIsAvailableAttribute()
    {
        return $this->stock_status === 'in_stock' && $this->stock_quantity > 0;
    }
}
