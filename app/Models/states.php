<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class states extends Model
{
    //
    // The table associated with the model (optional if table name is 'states')
    protected $table = 'states';
    // Fillable fields to allow mass assignment
    protected $fillable = [
        'name',
        'slug',
        'country_id'
    ];
    // Relationship with Country
    public function country()
    {
        return $this->belongsTo(countries::class);
    }
}
