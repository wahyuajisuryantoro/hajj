<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'name', 'code_category', 'content', 'picture', 'url', 'publish'
    ];

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'code_category', 'code');
    }

    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : 'Tidak ada kategori';
    }

    public function scopePublished($query)
    {
        return $query->where('publish', 1);
    }
}
