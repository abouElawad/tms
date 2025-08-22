<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    public function assignPermission(Request $request,$user)
    {
      $user= User::find($user);
      if(isNull($user))
      {
        return $this->apiResponse(404, 'Task not found', null, null);
      }
    }

    public function removePermission(Request $request,$user)
    {
      
    }
}
