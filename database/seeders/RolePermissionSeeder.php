<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Enums\PermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = PermissionEnum::cases();
        $owner = Role::where('name','owner')->first();
        foreach($permissions as $permission){
          Permission::create(['name'=>$permission->value]);
        }

        $owner->permissions()->attach(Permission::all());
    }
}
