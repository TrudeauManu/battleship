<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder de la base de données.
 *
 * @author Emmanuel Trudeau & Marc-Alexandre Bouchard
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed la base de donnée de l'application.
     */
    public function run(): void
    {
        User::factory(2)->create();
    }
}
