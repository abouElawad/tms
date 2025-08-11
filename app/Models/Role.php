<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, Notifiable;
  
      protected $fillable = ['name'];
    protected $hidden =['id','created_at','updated_at'];

    public function users()
    {
      return $this->hasMany(User::class);
    }
}
