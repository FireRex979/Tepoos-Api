<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postingan extends Model
{
    protected $table = 'postingans';

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'id_postingan');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function getUrlFoto(){
        return url('api/postingan/get-foto/'.$this->id).'?t=' . date('YmdHis');
    }
}
