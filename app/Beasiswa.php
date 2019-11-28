<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beasiswa extends Model
{

    protected $fillable = ['title', 'description', 'time'];


    public function users() {
        return $this->belongsToMany(User::class);
    }
}
