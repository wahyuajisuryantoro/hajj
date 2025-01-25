<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjrohCategory extends Model
{
   use HasFactory;

   protected $table = 'ujroh_categories';

   protected $fillable = [
       'code',
       'name',
       'desc',
       'value'
   ];

   protected $casts = [
       'value' => 'integer',
       'created_at' => 'datetime',
       'updated_at' => 'datetime'
   ];

   // Relationships
   public function ujrohs()
   {
       return $this->hasMany(Ujroh::class, 'code_category', 'code');
   }

   // Accessors
   public function getFormattedValueAttribute()
   {
       return 'Rp ' . number_format($this->value, 0, ',', '.');
   }
}