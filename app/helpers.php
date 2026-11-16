<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

function ends_with($s, $t)
{
    return \Illuminate\Support\Str::endsWith($s, $t);
}

function str_singular($s)
{
    return \Illuminate\Support\Str::singular($s);
}

function snake_case($s)
{
    return \Illuminate\Support\Str::snake($s);
}

function getSystemRoles(){
    $permission_seeder = new \Database\Seeders\PermissionSeeder;
    $roles = $permission_seeder->roles;
    $systemRoles = [];
    foreach($roles as $role => $permissions){
        $systemRoles[] = $role;
    }
    return $systemRoles;
}
