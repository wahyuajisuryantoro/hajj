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

    protected $table = 'mitras';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;


    public $timestamps = true;


    protected $attributes = [
        'level' => 'mitra',
        'status' => 'active'
    ];


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

    protected $casts = [
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    protected $hidden = [
        'password'
    ];


    public static $levelOptions = [
        'mitra',
        'pembina',
        'pembimbing'
    ];


    public static $sexOptions = [
        'L',
        'P'
    ];

    public static $statusOptions = [
        'active',
        'nonactive'
    ];

    public function parent()
    {
        return $this->belongsTo(Mitra::class, 'code_mitra');
    }

 
    public function children()
    {
        return $this->hasMany(Mitra::class, 'code_mitra');
    }
 
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'code_cabang', 'code');
    }


    public function category()
    {
        return $this->belongsTo(CustomerCategories::class, 'code_category', 'code');
    }


    public function program()
    {
        return $this->belongsTo(Program::class, 'code_program', 'code');
    }


    public function city()
    {
        return $this->belongsTo(City::class, 'code_city', 'id');
    }


    public function province()
    {
        return $this->belongsTo(Province::class, 'code_province', 'id');
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($mitra) {
            if ($mitra->picture_profile) {
                UploadFile::delete('mitra/profile', $mitra->picture_profile);
            }
            if ($mitra->picture_ktp) {
                UploadFile::delete('mitra/ktp', $mitra->picture_ktp);
            }
        });
    }


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

 
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }


    public function getPhoneNumberAttribute()
    {
        return '+62' . ltrim($this->phone, '0');
    }

  
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