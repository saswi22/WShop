<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $doctors = [
            [
                'name' => 'Dr. Ahmad Wijaya',
                'specialization' => 'Cardiologist',
                'phone' => '081234567890',
                'email' => 'ahmad@rumahsakit.com',
                'photo' => 'https://via.placeholder.com/300x300?text=Dr+Ahmad',
                'bio' => 'Dokter spesialis jantung dengan pengalaman 15 tahun',
                'experience' => '15 years',
            ],
            [
                'name' => 'Dr. Siti Nurhaliza',
                'specialization' => 'Pediatrician',
                'phone' => '081298765432',
                'email' => 'siti@rumahsakit.com',
                'photo' => 'https://via.placeholder.com/300x300?text=Dr+Siti',
                'bio' => 'Dokter anak profesional dengan dedikasi tinggi',
                'experience' => '12 years',
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'specialization' => 'Neurologist',
                'phone' => '081345678901',
                'email' => 'budi@rumahsakit.com',
                'photo' => 'https://via.placeholder.com/300x300?text=Dr+Budi',
                'bio' => 'Dokter saraf dengan keahlian khusus dalam neurologi modern',
                'experience' => '18 years',
            ],
        ];

        foreach ($doctors as $doctorData) {
            $doctor = Doctor::create($doctorData);

            // Add sample schedules
            Schedule::create([
                'doctor_id' => $doctor->id,
                'day' => 'Monday',
                'start_time' => '08:00',
                'end_time' => '12:00',
            ]);
            
            Schedule::create([
                'doctor_id' => $doctor->id,
                'day' => 'Wednesday',
                'start_time' => '14:00',
                'end_time' => '18:00',
            ]);
        }
    }
}
