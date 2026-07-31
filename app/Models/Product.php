<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'user_id',
        'product_name',
        'barcode',
        'sku',
        'cost_price',
        'sale_price',
        'stock_qty',
        'low_stock',
        'pro_image',
        'pro_des',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_qty' => 'integer',
        'low_stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(Purchase_detail::class, 'pro_id');
    }

    public function stockInDetails()
    {
        return $this->hasMany(Stock_in_detail::class);
    }

    public function stockOutDetails()
    {
        return $this->hasMany(Stock_out_detail::class, 'pro_id');
    }
}
