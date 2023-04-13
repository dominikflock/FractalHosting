<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        \App\Models\User::create([
            "firstname" => "Dominik",
            "lastname" => "Flock",
            "email" => "df@someoner.de",
            "password" => md5("test"),
        ]);

        $roleGroup__system = \App\Models\PermissionGroup::create([
            "name" => "System",
        ]);

        \App\Models\Permission::create([
            "name" => "Berechtigungen verwalten",
            "description" => "Gibt die Berechtigung, Berechtigungen zu verwalten.",
            "permission_group" => $roleGroup__system->id,
        ]);
        \App\Models\Permission::create([
            "name" => "Einstellungen verwalten",
            "description" => "Gibt die Berechtigung, Einstellungen zu verwalten.",
            "permission_group" => $roleGroup__system->id,
        ]);
        \App\Models\Permission::create([
            "name" => "Module verwalten",
            "description" => "Gibt die Berechtigung, Module zu verwalten.",
            "permission_group" => $roleGroup__system->id,
        ]);
        \App\Models\Permission::create([
            "name" => "Benutzer verwalten",
            "description" => "Gibt die Berechtigung, Benutzer zu verwalten.",
            "permission_group" => $roleGroup__system->id,
        ]);

        \App\Models\Module::create([
            "name" => "Testmodule",
            "description" => "Dies ist ein Testmodul welches für die Präsentation als Beispiel dient.",
            "controller_path" => "App\\Http\\ModuleControllers\\TestController",
            "fa_icon" => "fa-flask",
        ]);
    }
}
