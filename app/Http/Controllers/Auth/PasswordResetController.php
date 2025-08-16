<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\ForgotPasswordMail;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
  use ApiResponseTrait;
  public function forgotPassword(Request $request)
  {
    $validation = Validator::make($request->only('email'), [
      'email' => 'required|email|exists:users,email',
    ]);

    if ($validation->fails()) {
      $errors = collect($validation->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(422, 'validation error', $errors);
    }

    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
      return response()->json([
        'message' => 'Password reset email sent successfully.',
      ], 200);
    }
    return response()->json([
      'message' => 'Could not send password reset email.',
    ], 500);
  }

  public function resetPassword(Request $request)
  {
    $validation = Validator::make($request->all(), [
      'token' => 'required',
      'email' => 'required|email|exists:users,email',
      'password' => 'required|confirmed|min:8',
    ]);
    if ($validation->fails()) {
      $errors = collect($validation->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(422, 'validation error', $errors);
    }
    // $tokenData = DB::table('password_reset_tokens')
    //                ->where('email', $request->email)
    //                ->first();

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function (User $user, string $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));
        $user->save();
      }
    );

    return $status === Password::PasswordReset
      ? $this->apiResponse(201, 'password changed successfully', null, ['status' => __($status)])
      : $this->apiResponse(201, 'password not changed ', ['status' => __($status)]);
  }
}
