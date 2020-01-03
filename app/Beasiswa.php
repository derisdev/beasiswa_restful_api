<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beasiswa extends Model
{

    protected $fillable = ['title','organizer', 'time', 'location', 'description'];


    public function users() {
        return $this->belongsToMany(User::class);
    }
}
