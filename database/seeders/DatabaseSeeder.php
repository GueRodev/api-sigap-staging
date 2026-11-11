<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Ensayo AUTH (staging): crea un único usuario de prueba para
     * validar el login por cookies (Sanctum SPA + Fortify).
     * La contraseña viene del .env (SEED_TEST_PASSWORD) para no dejar
     * una contraseña conocida en el código; en staging debe ser fuerte.
     * updateOrCreate: se puede ejecutar varias veces sin duplicar.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SEED_TEST_EMAIL', 'prueba@ucr.ac.cr')],
            [
                'name' => 'Usuario de Prueba',
                // El modelo User hashea la contraseña automáticamente (cast 'hashed').
                'password' => env('SEED_TEST_PASSWORD', 'password'),
            ],
        );
    }
}