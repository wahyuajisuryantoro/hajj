<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    use HasFactory;

    protected $table = 'testimonis';

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'content', 'picture', 'url_youtube', 'publish'
    ];
}
