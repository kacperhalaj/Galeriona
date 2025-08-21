<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $users = User::where('role', 'user')
            ->when($keyword, function ($query, $keyword) {
                return $query->where('username', 'like', "%$keyword%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.manage.clients.clients', compact('users', 'keyword'));
    }

    public function create()
    {
        return view('admin.manage.clients.create');
    }

    private function validateUser(Request $request, $userId = null, $isAddressRequired = true)
    {
        $uniqueUsername = 'unique:users,username';
        $uniqueEmail = 'unique:users,email';
        if ($userId) {
            $uniqueUsername .= ',' . $userId;
            $uniqueEmail .= ',' . $userId;
        }

        $rules = [
            'username' => 'required|string|max:255|' . $uniqueUsername,
            'email' => 'required|email|max:255|' . $uniqueEmail,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => ($userId ? 'nullable' : 'required') . '|string|min:6|confirmed',
        ];

        if ($isAddressRequired) {
            $rules['city'] = 'required|string|max:255';
            $rules['postal_code'] = 'required|string|max:255';
            $rules['street'] = 'required|string|max:255';
            $rules['house_number'] = 'required|string|max:255';
            $rules['apartment_number'] = 'nullable|string|max:255';
        }

        return $request->validate($rules);
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

        $validated = $this->validateUser($request, null, true);

        $user = new User();
        $this->fillUserData($user, $validated);
        $user->role = 'user';
        $user->save();


        if (isset($validated['city'])) {
            $user->addresses()->create([
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'street' => $validated['street'],
                'house_number' => $validated['house_number'],
                'apartment_number' => $validated['apartment_number'] ?? null,
            ]);
        }

        return redirect()->route('admin.manage.users.index')->with('success', 'Użytkownik został dodany.');
    }

    public function show(User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }
        return view('admin.manage.clients.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }
        return view('admin.manage.clients.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }


        $validated = $this->validateUser($request, $user->id, false);
        $this->fillUserData($user, $validated);
        $user->save();



        return redirect()->route('admin.manage.users.index')->with('success', 'Użytkownik został zaktualizowany.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }

        $user->addresses()->delete();
        $user->delete();
        return redirect()->route('admin.manage.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}
