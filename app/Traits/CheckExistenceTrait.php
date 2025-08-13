<?php

namespace App\Traits;

trait checkExistenceTrait
{
  use ApiResponseTrait;
  public function checkUserLogin()
  {
    if (!auth()->user()) {
      return $this->apiResponse(403 ,'access denied, please login or register');
    }
  }
}
