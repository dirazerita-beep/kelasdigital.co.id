<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings', [
            'payment_method' => Setting::get('payment_method', 'manual'),
            'bank_name' => Setting::get('bank_name', ''),
            'bank_account_number' => Setting::get('bank_account_number', ''),
            'bank_account_name' => Setting::get('bank_account_name', ''),
            'whatsapp_number' => Setting::get('whatsapp_number', ''),
            'whatsapp_message_template' => Setting::get('whatsapp_message_template', ''),
            'midtrans_server_key' => Setting::get('midtrans_server_key', ''),
            'midtrans_client_key' => Setting::get('midtrans_client_key', ''),
            'midtrans_is_production' => Setting::get('midtrans_is_production', '0'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => 'required|in:midtrans,manual',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:20',
            'whatsapp_message_template' => 'nullable|string|max:1000',
            'midtrans_server_key' => 'nullable|string|max:200',
            'midtrans_client_key' => 'nullable|string|max:200',
            'midtrans_is_production' => 'nullable|in:0,1',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, (string) ($value ?? ''));
        }
        Setting::set('midtrans_is_production', $data['midtrans_is_production'] ?? '0');

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Pengaturan pembayaran berhasil disimpan.');
    }
}
