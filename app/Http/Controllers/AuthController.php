<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
  use ApiResponseTrait;
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    // $this->middleware('auth:api', ['except' => ['login','register']]);
  }



  public function register(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6',
      'role_id' => 'required|exists:roles,id',

    ]);

    if ($validator->fails()) {
      $errors = collect($validator->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field

      return $this->apiResponse(422, 'validation error', $errors);
    }


    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role_id' => $request->role_id
    ]);

    try {
      $token = JWTAuth::getToken()?->get();
    } catch (JWTException $e) {
      // return response()->json(['error' => 'Could not create token'], 500);
      return $this->apiResponse(500, 'Could not create token',);
    }
    $data = array_merge($user->toArray(), ['role' => $user->role->name], ['token' => $token]);
    return $this->apiResponse(201, "user : '{$user->name}' has been created successfully", null, $data);
  }

  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\Response
   */
  public function login()
  {
    $credentials = request(['email', 'password']);

    if (! $token = auth()->attempt($credentials)) {
      return $this->apiResponse(401, 'not authorized', 'Invalid credentials');
    }

    $data = array_merge(Auth::user()->toArray(), ['role' => Auth::user()->role->name], ['token' => $token]);
    return $this->apiResponse(200, 'welcome back', null, $data);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\Response
   */
  // public function me()
  // {


  //   try {
  //     if ($user = (auth()->user()))

  //       return $this->apiResponse(200, 'your data fetched successfully', null, $user);
  //   } catch (JWTException $e) {
  //     return $this->apiResponse(200, null, $e->getMessage(), null);
  //   }
  //   return $this->apiResponse(200, 'can\'t fetch your data ', 'no logged user');
  // }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\Response
   */
  public function logout()
  {
    try {
      if (auth()->user()) {
        JWTAuth::invalidate(JWTAuth::getToken());
      } else {
        return $this->apiResponse(200, 'logged out fails', 'no currently logged user', null);
      }
    } catch (JWTException $e) {
      return $this->apiResponse(200, 'logged out fails', $e->getMessage(), null);
    }
    return $this->apiResponse(200, 'Successfully logged out', null, null);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\Response
   */
  public function refresh()
  {
    $token = auth()->refresh();
    return $this->apiResponse(200, 'new token has been generated successfully', null, ['token' => $token]);
    // return $this->respondWithToken(auth()->refresh());
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ]);
  }

  public function getAuthenticatedUser()
  {
    // dd(JWTAuth::getToken());
    if ($user = (auth()->user())) {
      try {
        $data = array_merge($user->toArray(), ['role' => Auth::user()->role->name], ['token' =>  JWTAuth::getToken()?->get()]);
        return $this->apiResponse(200, 'get the current authenticated user', null, $data);
      } catch (JWTException $e) {
        return $this->apiResponse(200, null, $e->getMessage(), null);
      }
    } else {
      return $this->apiResponse(200, 'can\'t fetch your data ', 'no logged user');
    }
  }


  public function updateUser(Request $request)
  {
    if ($user = auth()->user()) {
      $validation = Validator::make($request->all(), [
        'name' => 'string|min:3|max:255',
        'email' => 'email|unique:users,email,' . $user->id,
        // 'password'=>'current_password:api',
        'role_id' => 'exists:roles,id',
      ]);



      if ($validation->fails()) {
        $errors = collect($validation->messages())
          ->map(fn($messages) => $messages[0]); // get first error message for each field
        return $this->apiResponse(400, null, $errors, null);
      }

      $user->update($request->all());
      $userRole = $user->role->name;

      $data = array_merge($user->toArray(), ['role' => $userRole], ['token' => $request->bearerToken()]);

      return $this->apiResponse(200, 'user updated successfully', null, $data);
    } else {
      return $this->apiResponse(200, 'can not preform an update', 'no logged user to update');
    }
  }
}
