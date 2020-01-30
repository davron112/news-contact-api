<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    public $timestamps = true;

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
