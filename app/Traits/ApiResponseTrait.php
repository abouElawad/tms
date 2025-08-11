<?php

namespace App\Traits;
Trait ApiResponseTrait
{
  public function apiResponse($code=200,$message=null, $errors=null, $data=null)
  {
    $response=[
      'code' => $code,
      'message' => $message,
    ];

    if(is_null($data) && !is_null($errors))
    {
      $response['errors'] = $errors;
    }elseif(is_null($errors) && !is_null($data)){
      $response['data'] = $data;
    }
    return response($response,200);
  }
}