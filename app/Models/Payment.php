<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

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

    protected $casts = [
        'value' => 'integer',
        'harga_program' => 'integer',
        'tanggal_transaksi' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'code_jamaah', 'code');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'code_customer', 'code');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_payment', $status);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function getFormattedHargaProgramAttribute()
    {
        return 'Rp ' . number_format($this->harga_program, 0, ',', '.');
    }

    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal_transaksi ? $this->tanggal_transaksi->format('d M Y') : '-';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'dp' => '<span class="badge rounded-pill bg-label-warning">DP</span>',
            'angsuran' => '<span class="badge rounded-pill bg-label-info">Angsuran</span>',
            'pelunasan' => '<span class="badge rounded-pill bg-label-success">Pelunasan</span>'
        ];

        return $labels[$this->status_payment] ?? '';
    }

    // Helper Methods
    public static function getTotalPaymentsByJamaah($codeJamaah)
    {
        return self::where('code_jamaah', $codeJamaah)->sum('value');
    }

    public static function getRemainingPayment($codeJamaah)
    {
        $payment = self::where('code_jamaah', $codeJamaah)->first();
        if (!$payment) return 0;

        $totalPaid = self::getTotalPaymentsByJamaah($codeJamaah);
        return $payment->harga_program - $totalPaid;
    }
}