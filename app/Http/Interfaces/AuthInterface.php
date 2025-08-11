<?php 

namespace App\Http\Interfaces;

use Illuminate\Http\Request;

interface AuthInterface
{
  public function register(Request $request);
  public function loginFromRepository(Request $request);
  public function logoutFromRepository();
  public function getUserFromRepository();
  public function updateUserFromRepository(Request $request);

}