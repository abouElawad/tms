<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'role_id',
    'project_id',
    'user_id',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'email_verified_at',
    'created_at',
    'updated_at',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function role()
  {
    return $this->belongsTo(Role::class);
  }
  public function projects()
  {
    return $this->hasMany(Project::class);
  }

  public function projectMembers()
  {
    return $this->belongsToMany(
      Project::class,
      'project_user',
      'project_id',
      'user_id',
      'id',
      'id'
    )
      ->withPivot('role_id');
  }

 

  public function assignedTasks()
  {
    return $this->hasMany(Task::class, 'assigned_to', 'id');
  }


   public function roles()
  {
    return $this->belongsToMany(User::class);
  }
  public function permissions()
  {
    return $this->belongsToMany(Permission::class);
  }

  public function getAllPermissions()
  {
    $userPermissions = $this->permissions->pluck('name')->toArray();
    $userPermissions = $this->roles->flatMap(fn($role)=> $role->permissions->pluck('name')->toArray());
    return array_unique(array_merge($userPermissions,$userPermissions));
  }
  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
    return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }
 
}
