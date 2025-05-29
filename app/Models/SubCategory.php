<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    //
    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'description',
    ];
    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
