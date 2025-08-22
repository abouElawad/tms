<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignPermissionRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
  use ApiResponseTrait;
  public function assignPermission(AssignPermissionRequest $request,$user)
  {
      $user= User::find($user);
      if(is_null($user))
        {
          return $this->apiResponse(404, 'user not found', null, null);
          
        }

      $permissions = $request->input('permissions',[]);
      $user->permissions()->sync($permissions);
        $userPermissions = $user->permissions()->pluck('name')->toArray();
        
      return $this->apiResponse(201,'permissions assigned successfully',null, ['user' => $user->only(['id','name']), // keep user fields
        'permissions' => $userPermissions]);
    }

    public function removePermission(Request $request,$user)
    {
      $user= User::find($user);
      if(is_null($user))
      {
        return $this->apiResponse(404, 'user not found', null, null);

      }
      $permissions = $request->input('permissions',[]);
      $user->permissions()->detach($permissions);
        $userPermissions = $user->permissions()->pluck('name')->toArray();

        return $this->apiResponse(201,'permissions removed successfully',null,['user' => $user->only(['id','name']), // keep user fields
        'permissions' => $userPermissions]);
    }
}
