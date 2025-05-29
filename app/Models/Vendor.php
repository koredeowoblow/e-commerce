<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Vendor extends Model  implements JWTSubject
{
    //
    protected $fillable = [
        'vendor_name',
        'slug',
        'email',
        'phone_number',
        'company_name',
        'description',
        'address',
        'logo',
        'status',
        'is_verified',
        'email_verified',
        'email_verify_token',
        'email_verified_at',
        'password',
        'profile_image',
        'country_id',
        'state_id',
        'city_id'
    ];
    protected $hidden = [
        'is_verified',
        'logo',
        'profile_image',
        'email_verified',
        'email_verify_token',
        'email_verified_at',
        'password',
        'created_at',
        'updated_at',
        'state_id',
        'city_id',
        'country_id',

    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function product()
    {
        return $this->hasmany(Product::class);
    }
    public function countries()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function states()
    {
        return $this->belongsTo(States::class, 'state_id');
    }

    public function cities()
    {
        return $this->belongsTo(City::class , 'city_id');   
    }
}
