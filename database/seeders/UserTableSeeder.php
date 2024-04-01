<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(['name'=>'administrator','email'=>'admin@gmail.com','password'=>bcrypt('password')]);

        //assign permission to role
        $role=Role::find(1);
        $permission=Permission::all();

        $role->syncPermissions($permission);

        //assign role with permission to user
        $user=User::find(1);
        $user->assignRole($role->name);
    }
}
