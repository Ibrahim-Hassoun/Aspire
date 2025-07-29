<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'category',
        'description',
        'price',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    // Scope for filtering by category
    public function scopeByCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', 'like', '%' . $category . '%');
        }
        return $query;
    }

    // Scope for searching by name or description
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    // Scope for price range filtering
    public function scopePriceRange($query, $minPrice, $maxPrice)
    {
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    // Scope for low stock filtering
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('quantity', '<=', $threshold);
    }
}
