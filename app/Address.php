<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $table = 'addresses';
    protected $fillable = ['user_id','address','main'];


    public function user(){
        return $this->belongsTo('App\User');
    }

}
