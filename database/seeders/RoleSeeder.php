<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $user = Role::create(['name' => 'user']);

        // إنشاء الصلاحيات
        $permissions = [
            'view_contracts',
            'create_contracts',
            'edit_contracts',
            'delete_contracts',
            'manage_users',
            'manage_roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إعطاء جميع الصلاحيات للمدير
        $admin->givePermissionTo(Permission::all());
        
        // إعطاء صلاحيات محددة للمدير
        $manager->givePermissionTo([
            'view_contracts',
            'create_contracts',
            'edit_contracts',
        ]);

        // إعطاء صلاحيات محددة للمستخدم العادي
        $user->givePermissionTo([
            'view_contracts',
        ]);
    }
} 