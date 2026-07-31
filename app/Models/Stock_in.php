<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock_in extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'supplier_id',
        'purchase_id',
        'stock_in_date',
        'status',
        'in_note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'stock_in_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function details()
    {
        return $this->hasMany(Stock_in_detail::class, 'stock_in_id');
    }
}
