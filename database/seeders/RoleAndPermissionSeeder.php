<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // user permissions
        $user_permission_names = [
            'create:user',
            'read:user',
            'update:user',
            'delete:user',
        ];

        $user_permissions = collect($user_permission_names)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($user_permissions->toArray());

        // inquiry permissions
        $inquiry_permission_names = [
            'create:inquiry',
            'read:inquiry',
            'update:inquiry',
            'delete:inquiry',
        ];

        $inquiry_permissions = collect($inquiry_permission_names)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($inquiry_permissions->toArray());

        // admin permissions
        $admin_permission_names = [
            'read:admin',
            'update:admin',
        ];

        $admin_permissions = collect($admin_permission_names)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($admin_permissions->toArray());

        // super-admin role
        $role_superadmin = Role::create(['name' => 'super-admin'])->givePermissionTo(Permission::all());

        // admin role
        $role_admin = Role::create(['name' => 'admin'])->syncPermissions(
            array_diff($user_permission_names, array('delete:user')),
            array_diff($inquiry_permission_names, array('create:inquiry')),
        );

        // agent role
        $role_agent = Role::create(['name' => 'agent'])->syncPermissions(array_diff($inquiry_permission_names, array('create:inquiry')));

        // create super-admin user
        User::factory()->create([
            'email' => 'superadmin@admin.com',
            'username' => 'superadmin',
            'is_admin' => true,
        ])->assignRole($role_superadmin);

        // create admin user
        User::factory()->create([
            'email' => 'admin@admin.com',
            'username' => 'administrator',
            'is_admin' => true,
        ])->assignRole($role_admin);

        // create agents
        $agents = User::factory()->count(10)->create();

        foreach ($agents as $agent) {
            $agent->assignRole($role_agent);
        }
    }
}
