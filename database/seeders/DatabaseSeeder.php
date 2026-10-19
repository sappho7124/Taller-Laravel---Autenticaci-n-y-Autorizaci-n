<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::create([
            'name' => 'Administrador Global',
            'email' => 'admin@eventhub.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Organizador A
        $organizadorA = User::create([
            'name' => 'Organizador A',
            'email' => 'organizadorA@eventhub.com',
            'password' => Hash::make('password123'),
            'role' => 'organizador',
        ]);

        // 3. Organizador B
        User::create([
            'name' => 'Organizador B',
            'email' => 'organizadorB@eventhub.com',
            'password' => Hash::make('password123'),
            'role' => 'organizador',
        ]);

        // 4. Asistente
        User::create([
            'name' => 'Usuario Asistente',
            'email' => 'asistente@eventhub.com',
            'password' => Hash::make('password123'),
            'role' => 'asistente',
        ]);

        // 5. Evento a nombre de Organizador A
        Evento::create([
            'user_id' => $organizadorA->id,
            'titulo' => 'Conferencia Tech Summit 2026',
            'descripcion' => 'El evento más grande de tecnología y desarrollo de software.',
            'cupo_maximo' => 250,
        ]);
    }
}