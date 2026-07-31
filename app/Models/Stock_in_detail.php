<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock_in_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'product_id',
        'quantity',
        'unit_cost',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function stockIn()
    {
        return $this->belongsTo(Stock_in::class, 'stock_in_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
