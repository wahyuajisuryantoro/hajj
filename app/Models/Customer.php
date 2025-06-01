<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $fillable = [
        'name',
        'code',
        'username',
        'password',
        'phone',
        'job',
        'email',
        'code_category',
        'code_cabang',
        'code_mitra',
        'code_city',
        'code_province',
        'note',
        'status',
        'status_prospek',
        'status_jamaah',
        'status_alumni',
        'address',
        'code_program',
        'NIK',
        'birth_place',
        'birth_date',
        'picture_ktp',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'code_mitra', 'code');
    }

    public function jamaah()
    {
        return $this->hasOne(Jamaah::class, 'code_customer', 'code')
            ->where('status', 'active');
    }

    public function getJamaahCodeAttribute()
    {
        if ($this->status_jamaah === 'active' && $this->jamaah) {
            return $this->jamaah->code;
        }
        return null;
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'code_cabang', 'code');
    }

    public function city()
    {
        return $this->belongsTo(Regency::class, 'code_city', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'code_province', 'id');
    }

    public function category()
    {
        return $this->belongsTo(CustomerCategories::class, 'code_category', 'code');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'code_customer', 'code');
    }

    public function paymentConfirms()
    {
        return $this->hasMany(PaymentConfirms::class, 'code_customer', 'code');
    }

     public function updateProgramQuota($programCode, $action = 'decrease')
    {
        try {
            $program = Program::where('code', $programCode)->first();
            
            if (!$program) {
                Log::warning('Program not found for quota update', ['program_code' => $programCode]);
                return;
            }

            if ($action === 'decrease') {
                if ($program->kuota !== null && $program->kuota > 0) {
                    $program->decrement('kuota');
                }
                if ($program->sisa_kursi !== null && $program->sisa_kursi > 0) {
                    $program->decrement('sisa_kursi');
                }
            } else if ($action === 'increase') {
                if ($program->kuota !== null) {
                    $program->increment('kuota');
                }
                if ($program->sisa_kursi !== null) {
                    $program->increment('sisa_kursi');
                }
            }

            Log::info('Program quota updated via Customer model', [
                'customer_code' => $this->code,
                'program_code' => $programCode,
                'action' => $action,
                'remaining_quota' => $program->kuota ?? 'N/A',
                'remaining_seats' => $program->sisa_kursi ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update program quota via Customer model: ' . $e->getMessage(), [
                'customer_code' => $this->code ?? 'unknown',
                'program_code' => $programCode,
                'action' => $action
            ]);
        }
    }
}
