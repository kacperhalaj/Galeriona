<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

class FollowersController extends Controller
{
    public function index()
    {
        $followers = auth()->user()->followers; // relacja followers w modelu User
        return view('seller.followers.index', compact('followers'));
    }
}
