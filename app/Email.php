<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    //
    protected $table = 'emails';
    protected $fillable = ['user_id', 'email'];
    protected $hidden = ['created_at','updated_at'];


    public function user(){
        return $this->belongsTo('App\User');
    }
}
