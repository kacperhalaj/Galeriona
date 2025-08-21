<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SellerFollowController extends Controller
{
    public function follow(User $seller)
    {
        auth()->user()->followedSellers()->syncWithoutDetaching($seller->id);
        return back();
    }

    public function unfollow(User $seller)
    {
        auth()->user()->followedSellers()->detach($seller->id);
        return back();
    }
}