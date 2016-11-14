<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $table = 'phones';
    protected $fillable = ['user_id', 'number', 'description', 'main'];
    protected $hidden = ['user_id', 'created_at', 'updated_at'];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
