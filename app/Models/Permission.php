<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable= ['name'];

    protected $hidden=['created_at','updated_at'];

    public function roles()
    {
      return $this->belongsToMany(Role::class);
    }

    public function users() {
      return $this->belongsToMany(user::class);

    }
}
