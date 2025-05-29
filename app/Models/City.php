<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class City extends Model
{
    //
    use HasFactory;

    protected $table = 'cities';  // Optional, if your table name follows Laravel's pluralization rule, it's not necessary.

    protected $fillable = [
        'name',
        'state_id',
    ];

    // Define the relationship with the State model (inverse of foreign key)
    public function state()
    {
        return $this->belongsTo(States::class, 'state_id');
    }
}
