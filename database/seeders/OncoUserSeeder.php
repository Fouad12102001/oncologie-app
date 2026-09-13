<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Oncologie\OncoUser;

class OncoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrateur Système',
                'email' => 'admin@clcc.dz',
                'password' => Hash::make('Admin@2024'),
                'role' => 'administrateur',
                'is_locked' => false,
                'login_attempts' => 0,
                'locked_at' => null,
            ],

            [
                'name' => 'Dr. Kahina Benzidane',
                'email' => 'medecin@clcc.dz',
                'password' => Hash::make('Medecin@2024'),
                'role' => 'medecin',
                'is_locked' => false,
                'login_attempts' => 0,
                'locked_at' => null,
            ],

            [
                'name' => 'Pharmacien Oncologie',
                'email' => 'pharmacien@clcc.dz',
                'password' => Hash::make('Pharma@2024'),
                'role' => 'pharmacien',
                'is_locked' => false,
                'login_attempts' => 0,
                'locked_at' => null,
            ],

            [
                'name' => 'Infirmier Chef',
                'email' => 'infirmier@clcc.dz',
                'password' => Hash::make('Infirm@2024'),
                'role' => 'infirmier',
                'is_locked' => false,
                'login_attempts' => 0,
                'locked_at' => null,
            ],
        ];

        foreach ($users as $user) {
            OncoUser::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('✅ Utilisateurs oncologie créés/mis à jour avec succès.');
    }
}