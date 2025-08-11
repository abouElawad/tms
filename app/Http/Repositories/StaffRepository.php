<?php 
namespace App\Http\Repositories;

use App\Http\Interfaces\StaffInterface;
use App\Http\Resources\StaffResource;
use App\Models\User;

class StaffRepository implements StaffInterface
{

  public function indexFromRepository(){
    $staff = User::with('role')->get();
    return StaffResource::collection($staff);
  }

  public function showFromRepository($staff) {
    
    $staff = User::find($staff);

    $staffRole = $staff->role->name;
    $staff =array_merge($staff->toArray(), ['role' => $staffRole]);
    return $staff;
  }
  public function storeFromRepository($request){

    $staff = User::create($request->all());
    return $staff;
  }
  public function updateFromRepository($request,$staff){
    
    $staff = User::find($staff);
    $staffRole = $staff->role->name;
    $staff->update($request->all());
    $staff->refresh();
    
    return  $staff = array_merge($staff->toArray(), ['role' => $staffRole]);

  }
  public function destroyFromRepository($staff){
    $staff = User::find($staff);
    $staff ->delete();
    return $staff;
  }

}