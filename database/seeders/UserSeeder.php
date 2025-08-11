<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $admin_id = Role::where('name','admin')->first()->id;
      User::create(['name'=>'abou',
                                'email'=>'a@a.com',
                                'password'=>Hash::make('12345678'),
                                'role_id' => $admin_id,
                              ]);

        User::factory(10)->create();
    }
}
