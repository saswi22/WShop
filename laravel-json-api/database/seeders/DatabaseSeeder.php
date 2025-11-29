<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            DoctorsSeeder::class,
            ServicesSeeder::class,
            FacilitiesSeeder::class,
            TestimonialsSeeder::class,
            PagesSeeder::class,
            PartnersSeeder::class,
        ]);
    }
}
