<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCategory extends Model
{
    use HasFactory;

    protected $table = 'program_categories';

    protected $fillable = [
        'code',
        'name',
        'desc'
    ];

    // Relationship with Program
    public function programs()
    {
        return $this->hasMany(Program::class, 'code_category', 'code');
    }
}
