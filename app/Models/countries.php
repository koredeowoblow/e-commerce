<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class countries extends Model
{

    use HasFactory;

    // The table associated with the model (optional if table name is 'countries')
    protected $table = 'countries';

    // Fillable fields to allow mass assignment
    protected $fillable = [
        'name',
        'slug'
    ];
}
