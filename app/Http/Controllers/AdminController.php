<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Get all settings
    public function getSettings()
    {
        $settings = GlobalSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    // Update settings (Admin only)
    public function updateSettings(Request $request)
    {
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            // Store arrays as JSON strings
            if (is_array($value)) {
                $value = json_encode($value);
            }

            GlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'Configurações atualizadas com sucesso']);
    }

    // Upload an image (Backgrounds, Banners, Icons)
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,avif|max:10240', // 10MB max
            'folder' => 'required|string'
        ]);

        $folder = $request->folder; // e.g., 'founde', 'banner', 'casino_icons'

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Move directly to public folder
            $file->move(public_path($folder), $filename);

            return response()->json([
                'url' => '/' . $folder . '/' . $filename
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
