<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get all settings grouped
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        
        $formatted = [];
        foreach ($settings as $group => $items) {
            $formatted[$group] = [];
            foreach ($items as $item) {
                $formatted[$group][$item->key] = $item->value;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $formatted,
        ]);
    }

    /**
     * Update multiple settings
     */
    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            // Determine group from key prefix
            $group = 'general';
            if (strpos($key, 'hero_') === 0) {
                $group = 'hero';
            } elseif (strpos($key, 'contact_') === 0) {
                $group = 'contact';
            }

            $type = 'text';
            if (strpos($key, 'logo') !== false || strpos($key, 'image') !== false) {
                $type = 'image';
            } elseif (strpos($key, 'description') !== false || strpos($key, 'text') !== false) {
                $type = 'textarea';
            }

            Setting::set($key, $value, $type, $group);
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }

    /**
     * Get public settings (for frontend)
     */
    public function public()
    {
        $settings = Setting::all();
        
        $data = [];
        foreach ($settings as $setting) {
            $data[$setting->key] = $setting->value;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
