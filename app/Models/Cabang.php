<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cabang extends Model
{
    use HasFactory;
    
    protected $table = 'cabangs';

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address',
        'code_city',
        'code_province'
    ];
}
