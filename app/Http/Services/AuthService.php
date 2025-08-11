<?php 
namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Interfaces\AuthInterface;
use App\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
  use ApiResponseTrait;

  public function __construct(public AuthInterface $authInterface)
  {
    
  }
  public function registerFromService($request){
    $validator = Validator::make($request->all(),[
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6',
      ]);

      if ($validator->fails())
      {
        $errors = collect($validator->errors()->messages())
                ->map(fn($messages) => $messages[0]) ;// get first error message for each field
          
          return $this->apiResponse(422,'validation error',$errors);
      }
  

      $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'phone' => $request->phone,
      'role_id' => $request->role_id
    ]);

    try {
      $token = JWTAuth::fromUser($user);
    } catch (JWTException $e) {
      // return response()->json(['error' => 'Could not create token'], 500);
      return $this->apiResponse(500,'Could not create token',);
    }

    return $this->apiResponse(201,"user : '{$user->name}' has been created successfully",null ,$user);

  }

    public function loginFromService(Request $request)
  {
    $credentials = $this->authInterface->loginFromRepository($request);

    try {
      if (!$token = JWTAuth::attempt($credentials)) 
      {
        return $this->apiResponse(401,'not authorized','Invalid credentials');
      }
    } catch (JWTException $e) {
        return $this->apiResponse(500,'not authorized',$e);
        // return $this->apiResponse(500,'not authorized','Could not create token');

    }
    // dd(Auth::user()->load('role:id,name'));
    $data = array_merge(Auth::user()->toArray() ,['role'=>Auth::user()->role->name], ['token' => $token]);
     return $this->apiResponse(200,'welcome back',null, $data);
  }

  public function logoutFromService(){

    try {
        $this->authInterface->logoutFromRepository();
    } catch (JWTException $e) {
      return response()->json(['error' => 'Failed to logout, please try again'], 500);
    }
    return response()->json(['message' => 'Successfully logged out']);
  }


  public function getUserFromService(){
     try {
      $user = $this->authInterface->getUserFromRepository();

      $userRole = $user->role->name;

      $user =array_merge($user->toArray(), ['role' => $userRole]);
      if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
      }
      return $this->apiResponse(200,'success',null,$user);
    } catch (JWTException $e) {
      return $this->apiResponse(200,'failed',$e,null); 
     
    }

  }

   
}