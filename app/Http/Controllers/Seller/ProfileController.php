<?php
namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\SellerDescription;


class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $description = $user->sellerDescription ? $user->sellerDescription->short_description : '';
        return view('seller.manage.update', [
            'user' => $user,
            'seller_description' => $description,
        ]);
        return view('seller.manage.update', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username'   => 'required|string|max:255|unique:users,username,' . $user->id,
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'   => 'nullable|string|min:6|confirmed',
            'seller_description' => 'required|string|max:255',
        ]);

        $user->username   = $validated['username'];
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // Zapisz lub zaktualizuj opis sprzedawcy
        SellerDescription::updateOrCreate(
            ['user_id' => $user->id],
            ['short_description' => $validated['seller_description']]
        );

        return back()->with('success', 'Dane zostały zaktualizowane.');
    }
}
