<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $fillable = [
        'code',
        'code_jamaah',
        'code_program',
        'code_customer',
        'desc',
        'value',
        'harga_program',
        'status_payment',
        'picture_scan',
        'tanggal_transaksi',
        'code_transaksi'
    ];

    protected $dates = [
        'tanggal_transaksi',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'value' => 'integer',
        'harga_program' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'code_customer', 'code');
    }
}
