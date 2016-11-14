<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    //
    protected $table = 'socials';
    protected $fillable = ['user_id', 'uri', 'aplication'];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
