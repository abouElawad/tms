<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, Notifiable;

    protected $fillable=['name','description','status','user_id','project_id','role_id'];
    protected $hidden=['created_at','update_at'];
    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function users()
    {
      return $this->belongsToMany(User::class)->withPivot('role_id');
    }
}
