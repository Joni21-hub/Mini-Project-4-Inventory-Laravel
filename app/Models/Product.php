<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'price',
        'stock',
        'minimum_stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers')
                    ->withPivot('supplier_price')
                    ->withTimestamps();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock && $this->stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out';
        }
        if ($this->stock <= $this->minimum_stock) {
            return 'low';
        }
        return 'available';
    }

    /**
     * Resolves realistic, high-resolution commercial product photography.
     */
    public function getImageUrlAttribute(): string
    {
        $text = strtolower($this->name . ' ' . $this->sku . ' ' . ($this->category->name ?? ''));

        if (str_contains($text, 'thinkpad') || str_contains($text, 'laptop')) {
            return 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'monitor') || str_contains($text, 'ultragear') || str_contains($text, 'layar')) {
            return 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'keychron') || str_contains($text, 'keyboard')) {
            return 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'mouse') || str_contains($text, 'mx master')) {
            return 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'router') || str_contains($text, 'mikrotik') || str_contains($text, 'switch') || str_contains($text, 'jaringan') || str_contains($text, 'networking')) {
            return 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'samsung 970') || str_contains($text, 'nvme') || str_contains($text, 'ssd') || str_contains($text, 'storage') || str_contains($text, 'harddisk')) {
            return 'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'webcam') || str_contains($text, 'kamera') || str_contains($text, 'c922')) {
            return 'https://images.unsplash.com/photo-1588702547923-7093a6c3ba33?w=800&auto=format&fit=crop&q=80';
        }
        if (str_contains($text, 'tinta') || str_contains($text, 'printer') || str_contains($text, 'kantor') || str_contains($text, 'kertas')) {
            return 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=800&auto=format&fit=crop&q=80';
        }

        return 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=800&auto=format&fit=crop&q=80';
    }
}
