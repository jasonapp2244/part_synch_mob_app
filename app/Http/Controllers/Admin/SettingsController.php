<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        // Default settings structure
        $defaults = [
            'general' => [
                'site_name' => 'Part Synch',
                'site_email' => '',
                'site_phone' => '',
                'currency' => 'USD',
                'currency_symbol' => '$',
            ],
            'commission' => [
                'vendor_commission_rate' => '10',
                'commission_type' => 'percentage',
            ],
            'orders' => [
                'min_order_amount' => '0',
                'tax_rate' => '0',
                'shipping_default' => '0',
            ],
        ];

        // Merge defaults with DB values
        $currentSettings = [];
        foreach ($defaults as $group => $keys) {
            foreach ($keys as $key => $default) {
                $currentSettings[$key] = Setting::get($key, $default);
            }
        }

        return view('admin.settings', compact('currentSettings'));
    }

    public function update(Request $request)
    {
        $settingKeys = [
            'site_name' => 'general',
            'site_email' => 'general',
            'site_phone' => 'general',
            'currency' => 'general',
            'currency_symbol' => 'general',
            'vendor_commission_rate' => 'commission',
            'commission_type' => 'commission',
            'min_order_amount' => 'orders',
            'tax_rate' => 'orders',
            'shipping_default' => 'orders',
        ];

        foreach ($settingKeys as $key => $group) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), $group);
            }
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }
}
