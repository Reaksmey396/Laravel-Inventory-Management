<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock_out extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'updated_by',
        'stock_out_date',
        'status',
        'reason',
        'reference_no',
        'out_note',
    ];

    protected $casts = [
        'stock_out_date' => 'date',
    ];

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

    public function details()
    {
        return $this->hasMany(Stock_out_detail::class, 'stock_out_id');
    }
}
