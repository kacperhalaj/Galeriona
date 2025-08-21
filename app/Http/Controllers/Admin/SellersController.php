<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellersController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $users = User::where('role', 'seller')
            ->when($keyword, function ($query, $keyword) {
                return $query->where('username', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.manage.sellers.sellers', compact('users', 'keyword'));
    }

    public function create()
    {
        return view('admin.manage.sellers.create');
    }

    private function validateUser(Request $request, $userId = null)
    {
        $uniqueUsername = 'unique:users,username';
        $uniqueEmail = 'unique:users,email';
        if ($userId) {
            $uniqueUsername .= ',' . $userId;
            $uniqueEmail .= ',' . $userId;
        }

        return $request->validate([
            'username' => 'required|string|max:255|' . $uniqueUsername,
            'email' => 'required|email|max:255|' . $uniqueEmail,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => ($userId ? 'nullable' : 'required') . '|string|min:6|confirmed',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:255',
            'apartment_number' => 'nullable|string|max:255',
        ]);
    }

    private function fillUserData(User $user, array $validated)
    {
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);

        $user = new User();
        $this->fillUserData($user, $validated);
        $user->role = 'seller';
        $user->save();

        // Zapis adresu
        $user->addresses()->create([
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'],
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'apartment_number' => $validated['apartment_number'] ?? null,
        ]);

        return redirect()->route('admin.manage.sellers.index')->with('success', 'Sprzedawca został dodany.');
    }

    public function show(User $user)
    {
        if ($user->role !== 'seller') {
            abort(404);
        }
        return view('admin.manage.sellers.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->role !== 'seller') {
            abort(404);
        }
        return view('admin.manage.sellers.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'seller') {
            abort(404);
        }

        $validated = $this->validateUser($request, $user->id);
        $this->fillUserData($user, $validated);
        $user->save();

        // Aktualizacja adresu
        if ($user->addresses) {
            $user->addresses->update([
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'street' => $validated['street'],
                'house_number' => $validated['house_number'],
                'apartment_number' => $validated['apartment_number'] ?? null,
            ]);
        } else {
            $user->addresses()->create([
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'street' => $validated['street'],
                'house_number' => $validated['house_number'],
                'apartment_number' => $validated['apartment_number'] ?? null,
            ]);
        }

        return redirect()->route('admin.manage.sellers.index')->with('success', 'Sprzedawca został zaktualizowany.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'seller') {
            abort(404);
        }
        $user->addresses()->delete();
        $user->delete();
        return redirect()->route('admin.manage.sellers.index')->with('success', 'Sprzedawca został usunięty.');
    }
}
