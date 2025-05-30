<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfirms extends Model
{
    use HasFactory;

    protected $table = 'payment_confirms';
    
    protected $fillable = [
        'code',
        'code_jamaah',
        'code_program',
        'code_customer',
        'desc',
        'value',
        'status_payment',
        'picture_scan',
        'tanggal_transaksi',
        'code_transaksi'
    ];

    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'code_customer', 'code');
    }

    
    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }
}