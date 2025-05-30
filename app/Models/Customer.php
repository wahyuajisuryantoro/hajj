<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
