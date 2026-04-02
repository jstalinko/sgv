<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class WebSettingController extends Controller
{
    private $filePath = 'private/web-setting.json';

    public function index()
    {
        $settings = [];
        if (Storage::exists($this->filePath)) {
            $settings = json_decode(Storage::get($this->filePath), true);
        }

        return Inertia::render('Dashboard/WebSetting', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'website_name' => 'nullable|string',
            'short_description' => 'nullable|string',
            'website_url' => 'nullable|string',
            'contact_center' => 'nullable|string',
            'alamat' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string|email',
            'instagram' => 'nullable|string',
            'rekening_type' => 'nullable|string',
            'rekening_tujuan' => 'nullable|string',
            'qris_image' => 'nullable|image|max:2048',
            'iuran_bulanan' => 'nullable|numeric',
        ]);

        $settings = [];
        if (Storage::exists($this->filePath)) {
            $settings = json_decode(Storage::get($this->filePath), true) ?? [];
        }

        if ($request->hasFile('qris_image')) {
            $path = $request->file('qris_image')->store('public/qris');
            $validated['qris_image_path'] = Storage::url($path);
        } else {
            if (isset($settings['qris_image_path']) && !isset($validated['qris_image'])) {
                $validated['qris_image_path'] = $settings['qris_image_path'];
            }
        }
        
        unset($validated['qris_image']);

        $newSettings = array_merge($settings, $validated);
        Storage::put($this->filePath, json_encode($newSettings, JSON_PRETTY_PRINT));

        return back()->with('success', 'Web settings updated successfully.');
    }
}
