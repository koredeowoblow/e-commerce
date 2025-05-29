<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
