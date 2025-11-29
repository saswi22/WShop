<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        Schema::enableForeignKeyConstraints();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@jsonapi.com',
            'password' => bcrypt('secret')
        ]);

        User::create([
            'name' => 'Admin Rumah Sakit',
            'email' => 'admin@rumahsakit.com',
            'password' => bcrypt('admin123')
        ]);

        User::create([
            'name' => 'Admin RSIZ',
            'email' => 'rspkujtb@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('rsiz1231')
        ]);
    }
}
