<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jamaah extends Model
{
    use HasFactory;

    protected $table = 'jamaahs';

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'job',
        'city_program',
        'desc',
        'date_program',
        'value',
        'total_payment',
        'code_city',
        'code_province',
        'code_customer', 
        'code_program',
        'code_cabang',
        'code_mitra',
        'status_payment',
        'status_berangkat',
        'picture_profile',
        'picture_ktp',
        'status',
        'tahun_jamaah'
    ];

    protected $casts = [
        'date_program' => 'date',
        'value' => 'integer',
        'total_payment' => 'integer',
        'tahun_jamaah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $enumStatuses = [
        'status_payment' => ['dp', 'angsuran', 'lunas'],
        'status_berangkat' => ['belum', 'sedang', 'sudah'],
        'status' => ['active', 'nonactive']
    ];

    // Relationship dengan Mitra
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'code_mitra', 'code');
    }

    // Relationship dengan Program (jika ada)
    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }

    // Scope untuk filter jamaah aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk filter berdasarkan status pembayaran
    public function scopeWithPaymentStatus($query, $status)
    {
        return $query->where('status_payment', $status);
    }

    // Scope untuk filter berdasarkan status keberangkatan
    public function scopeWithDepartureStatus($query, $status)
    {
        return $query->where('status_berangkat', $status);
    }

    // Scope untuk filter berdasarkan tahun
    public function scopeByYear($query, $year)
    {
        return $query->where('tahun_jamaah', $year);
    }

    // Get Status Payment Label
    public function getStatusPaymentLabelAttribute()
    {
        $labels = [
            'dp' => 'Down Payment',
            'angsuran' => 'Angsuran',
            'lunas' => 'Lunas'
        ];

        return $labels[$this->status_payment] ?? $this->status_payment;
    }

    // Get Status Berangkat Label
    public function getStatusBerangkatLabelAttribute()
    {
        $labels = [
            'belum' => 'Belum Berangkat',
            'sedang' => 'Sedang Berangkat',
            'sudah' => 'Sudah Berangkat'
        ];

        return $labels[$this->status_berangkat] ?? $this->status_berangkat;
    }

    // Format Total Payment
    public function getFormattedTotalPaymentAttribute()
    {
        return 'Rp ' . number_format($this->total_payment, 0, ',', '.');
    }


}
