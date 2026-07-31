<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pur_id',
        'pro_id',
        'quantity',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'pur_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id');
    }
}
