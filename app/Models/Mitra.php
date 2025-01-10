<?php

namespace App\Models;

use App\Helpers\UploadFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mitra extends Authenticatable
{
    use Notifiable;
    protected $guard = 'mitra';
    /**
     * Nama tabel yang digunakan model ini
     */
    protected $table = 'mitras';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id';

    /**
     * Tipe data primary key
     */
    protected $keyType = 'int';

    /**
     * Increment ID secara otomatis
     */
    public $incrementing = true;

    /**
     * Menggunakan timestamps (created_at & updated_at)
     */
    public $timestamps = true;

    /**
     * Default values untuk kolom tertentu
     */
    protected $attributes = [
        'level' => 'mitra',
        'status' => 'active'
    ];

    /**
     * Daftar kolom yang bisa diisi secara massal
     */
    protected $fillable = [
        'code',
        'username',
        'password',
        'referral_code',
        'code_category',
        'code_cabang',
        'code_mitra',
        'level',
        'name',
        'NIK',
        'sex',
        'birth_place',
        'birth_date',
        'address',
        'code_city',
        'code_province',
        'phone',
        'email',
        'bank',
        'bank_number',
        'bank_name',
        'picture_profile',
        'picture_ktp',
        'status'
    ];

    /**
     * Kolom yang akan diubah tipe datanya
     */
    protected $casts = [
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Kolom yang harus dijaga agar tidak tampil dalam array/json
     */
    protected $hidden = [
        'password'
    ];

    /**
     * Validasi enum untuk kolom level
     */
    public static $levelOptions = [
        'mitra',
        'pembina',
        'pembimbing'
    ];

    /**
     * Validasi enum untuk kolom sex
     */
    public static $sexOptions = [
        'L',
        'P'
    ];

    /**
     * Validasi enum untuk kolom status
     */
    public static $statusOptions = [
        'active',
        'nonactive'
    ];

    public function parent()
    {
        return $this->belongsTo(Mitra::class, 'code_mitra');
    }

    /**
     * Relasi ke Mitra Children
     */
    public function children()
    {
        return $this->hasMany(Mitra::class, 'code_mitra');
    }
    /**
     * Relasi ke Cabang
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'code_cabang', 'code');
    }

    /**
     * Relasi ke Category
     */
    public function category()
    {
        return $this->belongsTo(CustomerCategories::class, 'code_category', 'code');
    }

    /**
     * Relasi ke Program
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }

    /**
     * Relasi ke City
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'code_city', 'code');
    }

    /**
     * Relasi ke Province
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'code_province', 'code');
    }

    /**
     * Boot method untuk model
     */
    protected static function boot()
    {
        parent::boot();

        // Delete files when model is deleted
        static::deleting(function ($mitra) {
            if ($mitra->picture_profile) {
                UploadFile::delete('mitra/profile', $mitra->picture_profile);
            }
            if ($mitra->picture_ktp) {
                UploadFile::delete('mitra/ktp', $mitra->picture_ktp);
            }
        });
    }

    /**
     * Helper method untuk update files
     */
    public function updateFiles($request)
    {
        if ($request->hasFile('picture_profile')) {
            if ($this->picture_profile) {
                UploadFile::delete('mitra/profile', $this->picture_profile);
            }
            $this->picture_profile = UploadFile::file($request->file('picture_profile'), 'mitra/profile');
        }

        if ($request->hasFile('picture_ktp')) {
            if ($this->picture_ktp) {
                UploadFile::delete('mitra/ktp', $this->picture_ktp);
            }
            $this->picture_ktp = UploadFile::file($request->file('picture_ktp'), 'mitra/ktp');
        }

        return $this->save();
    }

    /**
     * Scope query untuk mencari mitra aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query untuk mencari berdasarkan level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Accessor untuk format nomor telepon
     */
    public function getPhoneNumberAttribute()
    {
        return '+62' . ltrim($this->phone, '0');
    }

    /**
     * Accessor untuk nama lengkap dengan level
     */
    public function getFullTitleAttribute()
    {
        return $this->name . ' (' . ucfirst($this->level) . ')';
    }

      public function buildTree($mitraCode = null, $level = 0)
    {
        if (!$mitraCode) {
            $mitraCode = $this->code;
        }

        $mitra = $this->where('code', $mitraCode)->first();

        if (!$mitra) {
            return [];
        }

        $tree = [
            'id' => $mitra->id,
            'text' => $level . '. ' . $mitra->name ,
            'icon' => $level === 0 ? 'ri-user-star-line text-warning' : 'ri-user-line',
            'state' => ['opened' => true],
            'children' => []
        ];

        $children = $this->where('code_mitra', $mitraCode)->get();
        $childLevel = $level + 1;

        foreach ($children as $child) {
            $tree['children'][] = $this->buildTree($child->code, $childLevel);
        }

        return $tree;
    }
}