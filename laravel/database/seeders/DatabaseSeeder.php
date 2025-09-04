<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\QuestionCategorySeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed kategorie
        $this->call(CategorySeeder::class);
        
        // Seed přiřazení otázek ke kategoriím
        $this->call(QuestionCategorySeeder::class);
        
        // Seed test data pro uživatele ID 2
        $this->call(TestDataSeeder::class);
        
        // Seed admin uživatele
        $this->call(AdminSeeder::class);
    }
}
