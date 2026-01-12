<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);
        
        $oldSettings = Setting::pluck('value', 'key')->toArray();

        foreach ($settings as $key => $value) {
            // Handle checkbox values
            if (in_array($key, ['email_notifications', 'return_reminder', 'fine_notification', 'maintenance_mode'])) {
                $value = $request->has("settings.{$key}") ? '1' : '0';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Ensure unchecked checkboxes are set to 0
        $checkboxFields = ['email_notifications', 'return_reminder', 'fine_notification', 'maintenance_mode'];
        foreach ($checkboxFields as $field) {
            if (!isset($settings[$field])) {
                Setting::updateOrCreate(
                    ['key' => $field],
                    ['value' => '0']
                );
            }
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Setting::class,
            'description' => 'Memperbarui pengaturan sistem',
            'old_values' => $oldSettings,
            'new_values' => $settings,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
