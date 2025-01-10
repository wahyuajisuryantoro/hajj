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

    // Relasi ke Mitra
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'code_mitra', 'code');
    }

    // Relasi ke Cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'code_cabang', 'code');
    }

    // Relasi ke City
    public function city()
    {
        return $this->belongsTo(City::class, 'code_city', 'code');
    }

    // Relasi ke Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'code_province', 'code');
    }

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(CustomerCategories::class, 'code_category', 'code');
    }

    // Relasi ke Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }
}
