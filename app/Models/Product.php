<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($product) {
            // Check if the SKU is empty
            if (empty($product->slug)) {
                // Generate a random SKU and assign it to the model
                $product->slug = 'SKU_' . uniqid();
            }
        });
    }

}
