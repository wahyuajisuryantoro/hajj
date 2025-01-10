<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerCategories extends Model
{
    use HasFactory;
    
    protected $table = 'customer_categories';

    protected $fillable = [
        'code',
        'name',
        'desc',
    ];
}
