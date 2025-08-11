<?php 

namespace App\Http\Repositories;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Http\Interfaces\AuthInterface;

class AuthRepository implements AuthInterface
{
  public function register(Request $request){}
  public function loginFromRepository(Request $request)
  {
    return  $request->only('email', 'password');
  }
  public function logoutFromRepository(){
    
     JWTAuth::invalidate(JWTAuth::getToken());
  }
  public function getUserFromRepository(){
    return Auth::user();
  }
  public function updateUserFromRepository(Request $request){}

}