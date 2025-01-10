<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujroh extends Model
{
    use HasFactory;

    protected $table = 'ujrohs';

    protected $fillable = [
        'code',
        'code_category',
        'code_mitra',
        'code_customer',
        'code_program',
        'code_jamaah',
        'desc',
        'value',
        'status',
        'tanggal_transaksi',
        'bukti',
        'sisa_saldo',
        'code_transaksi'
    ];

    protected $casts = [
        'value' => 'integer',
        'sisa_saldo' => 'integer',
        'tanggal_transaksi' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'code_mitra', 'code');
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'code_jamaah', 'code');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }

    // Scopes
    public function scopeDebit($query)
    {
        return $query->where('status', 'debit');
    }

    public function scopeCredit($query)
    {
        return $query->where('status', 'credit');
    }

    public function scopeByMitra($query, $mitraCode)
    {
        return $query->where('code_mitra', $mitraCode);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
    }

    // Accessors & Mutators
    public function getFormattedValueAttribute()
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal_transaksi ? $this->tanggal_transaksi->format('d M Y') : '-';
    }

    // Static Methods for Calculations
    public static function getTotalBonus($mitraCode)
    {
        return self::byMitra($mitraCode)->debit()->sum('value');
    }

    public static function getTotalTransfer($mitraCode)
    {
        return self::byMitra($mitraCode)->credit()->sum('value');
    }

    public static function getCurrentBalance($mitraCode)
    {
        return self::getTotalBonus($mitraCode) - self::getTotalTransfer($mitraCode);
    }

    public static function getWeeklyComparison($mitraCode)
    {
        $thisWeek = self::byMitra($mitraCode)
            ->debit()
            ->where('tanggal_transaksi', '>=', now()->startOfWeek())
            ->sum('value');

        $lastWeek = self::byMitra($mitraCode)
            ->debit()
            ->whereBetween('tanggal_transaksi', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ])
            ->sum('value');

        $percentage = $lastWeek != 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

        return [
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'percentage' => $percentage
        ];
    }
}
