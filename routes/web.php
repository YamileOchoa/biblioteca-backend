<?php
/*
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

Route::get('/crear-roles', function () {

    Role::create(['name' => 'admin']);
    Role::create(['name' => 'user']);

    Permission::create(['name' => 'crear libros']);
    Permission::create(['name' => 'ver libros']);

    $admin = Role::findByName('admin');
    $admin->givePermissionTo(['crear libros', 'ver libros']);

    $user = Role::findByName('user');
    $user->givePermissionTo('ver libros');


    $usuario = User::find(1);
    $usuario->assignRole('admin');

    return 'Roles y permisos creados y asignados'; 
});*/

