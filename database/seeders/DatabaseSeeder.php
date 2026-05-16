<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Root seeder — calls all other seeders in the correct order.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BookSeeder::class,
        ]);
    }
}
