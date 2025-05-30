<?php

/*
 * This file is part of the IndoRegion package.
 *
 * (c) Azis Hapidin <azishapidin.com | azishapidin@gmail.com>
 *
 */

namespace App\Models;

use AzisHapidin\IndoRegion\Traits\DistrictTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Regency;
use App\Models\Village;

/**
 * District Model.
 */
class District extends Model
{
    use DistrictTrait;

    protected $table = 'districts';
  
    protected $hidden = [
        'regency_id'
    ];

   
    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }


    public function villages()
    {
        return $this->hasMany(Village::class);
    }
}
