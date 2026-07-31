<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock_out_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_id',
        'pro_id',
        'quantity',
        'out_cost',
        'reason',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'out_cost' => 'decimal:2',
    ];

    public function stockOut()
    {
        return $this->belongsTo(Stock_out::class, 'stock_out_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id');
    }
}
