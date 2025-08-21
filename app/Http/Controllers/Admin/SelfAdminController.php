<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SelfAdminController extends Controller
{
    public function update(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'username'   => 'required|string|max:255|unique:users,username,' . $admin->id,
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $admin->id,
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        $admin->username   = $validated['username'];
        $admin->first_name = $validated['first_name'];
        $admin->last_name  = $validated['last_name'];
        $admin->email      = $validated['email'];
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }
        $admin->save();

        return back()->with('success', 'Dane administratora zostały zaktualizowane.');
    }
}
