<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AppSetting;
use App\Models\Username;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateRegistrationController extends Controller
{
    public function show()
    {
        if (! AppSetting::bool('affiliate_registration_open', true)) {
            return view('affiliate.closed');
        }

        return view('affiliate.register');
    }

    public function submit(Request $request)
    {
        if (! AppSetting::bool('affiliate_registration_open', true)) {
            abort(403, 'Affiliate registration is currently closed.');
        }

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['required', 'string', 'min:7', 'max:20'],
            'username'       => [
                'required', 'string', 'max:50', 'alpha_dash',
                'unique:affiliates,username',
                'unique:customers,username',
                'unique:users,username',
            ],
            'bank_name'      => ['nullable', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:20'],
            'account_name'   => ['nullable', 'string', 'max:150'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);

        // Double-check global username registry
        if (! Username::isAvailable($data['username'])) {
            return back()->withErrors(['username' => 'This username is already taken.'])->withInput();
        }

        DB::transaction(function () use ($data) {
            $affiliate = Affiliate::create([
                'username'       => $data['username'],
                'name'           => $data['name'],
                'email'          => $data['email'],
                'phone'          => $data['phone'],
                'bank_name'      => $data['bank_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'account_name'   => $data['account_name'] ?? null,
                'notes'          => $data['notes'] ?? null,
                'status'         => 'pending',
            ]);

            Username::claim($affiliate->username, 'affiliate', $affiliate->id);
        });

        return redirect()->route('affiliate.register.success');
    }

    public function success()
    {
        return view('affiliate.success');
    }
}
