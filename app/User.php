<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surnames', 'user_type', 'color','email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at'
    ];

    /**
     * This mutator automatically hashes the password.
     *
     * @var string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }
    public function phones(){
        return $this->hasMany('App\Phone');
    }

    public function emails(){
        return $this->hasMany('App\Email');
    }

    public function socials(){
        return $this->hasMany('App\Social');
    }

    public function addresses(){
        return $this->hasMany('App\Address');
    }
}
