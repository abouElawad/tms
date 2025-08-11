<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UserRepositoryInterface;
use App\Http\Resources\UserResource;
use App\Models\Role;
use Response;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response as HttpResponse;

class UserRepository implements UserRepositoryInterface
{

  public function indexFromRepository()
  {
    $users = User::with('role')->get();
    
    return UserResource::collection($users);
  }
  public function showFromRepository($user) 
  {
      return  $user;
  }

  public function storeFromRepository( $request)
  {
  return User::create($request);
  }

  public function updateFromRepository($request,$user)
  {
    $user->update($request->all());
  return $user;
  }

  public function destroyFromRepository($user)
  {
    $user->delete();
    return $user;
  }

}