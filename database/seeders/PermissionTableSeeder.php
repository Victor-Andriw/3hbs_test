<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permission = Permission::create(['name' => 'get airports']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        $permission = Permission::create(['name' => 'create airports']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        $permission = Permission::create(['name' => 'edit airports']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        $permission = Permission::create(['name' => 'delete airports']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        
        $permission = Permission::create(['name' => 'get airlines']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        $permission = Permission::create(['name' => 'create airlines']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        $permission = Permission::create(['name' => 'edit airlines']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        $permission = Permission::create(['name' => 'delete airlines']);
        $permission->assignRole('admin');
        $permission->assignRole('operations');
        
        $permission = Permission::create(['name' => 'get flights']);
        $permission->assignRole('admin');
        $permission = Permission::create(['name' => 'create flights']);
        $permission->assignRole('admin');
        $permission = Permission::create(['name' => 'edit flights']);
        $permission->assignRole('admin');
        $permission = Permission::create(['name' => 'delete flights']);
        $permission->assignRole('admin');
    }
}
