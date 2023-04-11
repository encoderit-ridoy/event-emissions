<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionData = [
            ['name' => 'User Read', 'slug' => 'user_read'],
            ['name' => 'User View', 'slug' => 'user_view'],
            ['name' => 'User Create', 'slug' => 'user_create'],
            ['name' => 'User Update', 'slug' => 'user_update'],
            ['name' => 'User Destroy', 'slug' => 'user_destory'],
        ];

        foreach ($permissionData as $permission) {
            Permission::create($permission);
        }

        $roleData = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'User', 'slug' => 'user']
        ];

        foreach ($roleData as $role) {
            Role::create($role);
        }

        $permission = [
            'admin' => [
                'user_read',
                'user_view',
                'user_create',
                'user_update',
                'user_destory',
            ],
            'user' => [
                'user_read',
                'user_view',
                'user_create',
                'user_update',
                'user_destory',
            ]
        ];

        foreach (Role::all() as $role) {
            $permission_ids = Permission::whereIn('slug', $permission[$role->slug])->pluck('id')->toArray();
            $role->permissions()->sync($permission_ids);
        }
    }
}
