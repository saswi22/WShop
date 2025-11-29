<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Rumah Sakit', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'image', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Pelayanan Kesehatan Terbaik', 'type' => 'text', 'group' => 'general'],
            
            // Hero Section
            ['key' => 'hero_title', 'value' => 'Selamat Datang di Rumah Sakit Kami', 'type' => 'text', 'group' => 'hero'],
            ['key' => 'hero_description', 'value' => 'Kami berkomitmen untuk memberikan pelayanan kesehatan terbaik dengan teknologi modern dan tim profesional yang berpengalaman.', 'type' => 'textarea', 'group' => 'hero'],
            ['key' => 'hero_image', 'value' => '', 'type' => 'image', 'group' => 'hero'],
            ['key' => 'hero_button_1_text', 'value' => 'Lihat Layanan', 'type' => 'text', 'group' => 'hero'],
            ['key' => 'hero_button_1_link', 'value' => '/services', 'type' => 'text', 'group' => 'hero'],
            ['key' => 'hero_button_2_text', 'value' => 'Tim Dokter', 'type' => 'text', 'group' => 'hero'],
            ['key' => 'hero_button_2_link', 'value' => '/doctors', 'type' => 'text', 'group' => 'hero'],
            
            // Contact Info
            ['key' => 'contact_phone', 'value' => '(021) 123-4567', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_email', 'value' => 'info@rumahsakit.com', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => 'Jl. Kesehatan No. 123, Jakarta', 'type' => 'textarea', 'group' => 'contact'],
            ['key' => 'contact_hours', 'value' => 'Senin - Jumat: 08:00 - 20:00, Sabtu - Minggu: 08:00 - 16:00', 'type' => 'text', 'group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
