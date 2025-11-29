<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    public function run()
    {
        Testimonial::create([
            'patient_name' => 'Ibu Sari',
            'message' => 'Pelayanan sangat baik dan cepat. Dokternya ramah.',
            'photo' => 'https://via.placeholder.com/120?text=Sari',
            'is_approved' => true,
        ]);

        Testimonial::create([
            'patient_name' => 'Bapak Joko',
            'message' => 'Ruang bersih dan perawatnya sangat helpful.',
            'photo' => 'https://via.placeholder.com/120?text=Joko',
            'is_approved' => true,
        ]);
    }
}
