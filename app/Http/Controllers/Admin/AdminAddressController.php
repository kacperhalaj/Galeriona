<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with('addresses')->get();
        $selectedUser = null;
        $addresses = collect();

        if ($request->has('user_id')) {
            $selectedUser = User::with('addresses')->find($request->user_id);
            if ($selectedUser) {
                $addresses = $selectedUser->addresses;
            }
        }

        return view('admin.addresses.index', compact('users', 'selectedUser', 'addresses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $users = User::all();
        $selectedUserId = $request->query('user_id');
        return view('admin.addresses.create', compact('users', 'selectedUserId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:10',
            'apartment_number' => 'nullable|string|max:10',
        ]);

        Address::create($request->all());

        return redirect()->route('admin.addresses.index', ['user_id' => $request->user_id])
            ->with('success', 'Address created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Address $address)
    {
        $users = User::all();
        return view('admin.addresses.edit', compact('address', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Address $address)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:10',
            'apartment_number' => 'nullable|string|max:10',
        ]);

        $address->update($request->all());

        return redirect()->route('admin.addresses.index', ['user_id' => $address->user_id])
            ->with('success', 'Address updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        $userId = $address->user_id;
        $address->delete();

        return redirect()->route('admin.addresses.index', ['user_id' => $userId])
            ->with('success', 'Address deleted successfully.');
    }
}
