<?php

namespace App\Http\Services;

use Response;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Repositories\UserRepository;
use App\Http\Interfaces\UserRepositoryInterface;

class UserService {
  use ApiResponseTrait;

  public function __construct(public UserRepositoryInterface $repository){
    
  }
  public function indexFromService()
  {

    $users = $this->repository->indexFromRepository();

    return $this->apiResponse(200,
                              'all users',
                              null,
                              $users);;

  }
  public function showFromService(User $user) 
  {
    
    
    return $this->apiResponse(200,
                                  'single user',
                                  null,
                                  $this->repository->showFromRepository($user));

  }

  public function storeFromService(Request $request)
  {


  return $this->apiResponse(200,
                                      'all users',
                                      null,
                                      $this->repository->storeFromRepository($request));
  }

  public function updateFromService(Request $request,User $user)
  {

  return $this->apiResponse(200,
                            'user updated successfully',
                            null,
                            $this->repository->updateFromRepository($request,$user));
  }

  public function destroyFromService(User $user)
  {
    return $this->apiResponse(200,
                              $user->name .'has been deleted',
                              null,
                              $this->repository->destroyFromRepository($user));
  }




}