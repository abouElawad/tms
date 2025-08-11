<?php 


namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Interfaces\StaffInterface;
use Illuminate\Support\Facades\Validator;

class StaffService
{
  use ApiResponseTrait;

  public function __construct(public StaffInterface $staffRepository) {
    
  }
    public function indexFromService()
  {

    $staff = $this->staffRepository->indexFromRepository();

    return $this->apiResponse(200,
                              'all staff',
                              null,
                              $staff);;

  }
  public function showFromService($staff) 
  {
    try {
      return $this->apiResponse(200,
                                  'single staff',
                                  null,
                                  $this->staffRepository->showFromRepository($staff));

    } catch (\Throwable $e) {

      return $this->apiResponse(404,
                                  'not registered member',
                                  $e->getMessage(),
                                  null);
    }
    
  }

  public function storeFromService(Request $request)
  { 
      $validation = Validator::make($request->all(),[
        'name'=>'required|string|min:3',
        'email'=>'required|email|unique:users,email',
        'password'=>'required|min:8',
        'phone'=>'sometimes|string|numeric|unique:users,phone',
        'role_id'=>'required|exists:roles,id',
      ]);
      if($validation->fails()){
        $errors = collect($validation->messages())
                          ->map(fn($messages) => $messages[0]) ;// get first error message for each field
        return $this->apiResponse( 400 , null,$errors,null);
      }
      
    return $this->apiResponse(200,
                                      'staff Added successfully',
                                      null,
                                      $this->staffRepository->storeFromRepository($request));
  }

  public function updateFromService(Request $request,$staff)
  {
     $validation = Validator::make($request->all(),[
        'name'=>'required|string|min:3',
        'email'=>'required|email|unique:users,email,'.$staff,
        'password'=>'current_password:api',
        'phone'=>'sometimes|string|numeric|unique:users,phone,'.$staff,
        'role_id'=>'required|exists:roles,id',
      ]);

      if($validation->fails()){
        $errors = collect($validation->messages())
                          ->map(fn($messages) => $messages[0]) ;// get first error message for each field
        return $this->apiResponse( 400 , null,$errors,null);
      }


  return $this->apiResponse(200,
                            'staff updated successfully',
                            null,
                            $this->staffRepository->updateFromRepository($request,$staff));
  }

  public function destroyFromService($staff)
  {

    try {
       return $this->apiResponse(200,
                              $staff->name .'has been deleted',
                              null,
                              $this->staffRepository->destroyFromRepository($staff));

    } catch (\Throwable $e) {

      return $this->apiResponse(404,
                                  'not registered member',
                                  $e->getMessage(),
                                  null);
    }
   
  }





}