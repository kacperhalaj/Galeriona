<?php

use App\Http\Controllers\Admin\AdminAddressController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\CategoryController;

use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\SellerFollowController;
use App\Http\Controllers\Client\FollowersController;
use App\Http\Controllers\Seller\ArtworkController as SellerArtworkController;

use App\Http\Controllers\Seller\ProfileController;
use App\Http\Controllers\Seller\FollowersController as SellerFollowersController;
use App\Http\Controllers\ArtworkController as PublicArtworkController;
use App\Http\Controllers\Admin\ArtworkController as AdminArtworkController;
use App\Http\Controllers\User\KolekcjeController;
use App\Models\Artwork;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Client\AddressController;

use App\Http\Controllers\Client\StatisticsController;
use App\Http\Controllers\Seller\StatisticsController as SellerStatisticsController;
use App\Http\Controllers\Admin\StatisticsController as AdminStatisticsController;

use App\Http\Controllers\Client\ClientWalletController;
use App\Http\Controllers\Seller\SellerFinancesController;
use App\Http\Controllers\Seller\FinanceController;


use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SellerController;


use App\Http\Controllers\Admin\SelfAdminController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $artworks = \App\Models\Artwork::where('is_priceless', 0)
                                     ->where('is_sold', false) 
                                     ->paginate(9);

    $cartArtworks = [];
    if (Auth::check() && Auth::user()->role === 'user') {
        $cart = Auth::user()->cart; 
        if ($cart) {
            $cart = $cart->load('items'); 
            $cartArtworks = $cart->items->pluck('artwork_id')->toArray();
        }
    }

    return view('home', compact('artworks', 'cartArtworks'));
})->name('home');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('login');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registerPost')->name('register');
    Route::post('/logout', 'logout')->name('logout');

    // TOTP Login Routes
    Route::get('/totp/login', 'showTotpLoginForm')->name('totp.login.form');
    Route::post('/totp/login', 'verifyTotpLogin')->name('totp.login.verify');

    // TOTP Setup Routes
    Route::get('/totp/setup', 'showTotpSetup')->name('totp.setup')->middleware('auth');
    Route::post('/totp/verify', 'verifyTotpSetup')->name('totp.verify')->middleware('auth');
    Route::post('/totp/enable', 'enableTotp')->name('totp.enable')->middleware('auth');
    Route::post('/totp/disable', 'disableTotp')->name('totp.disable')->middleware('auth');
});

Route::get('/register/seller', [AuthController::class, 'registerSeller'])->name('register.seller');

Route::get('/seller/panel', function () {
    return view('seller.panel');
})->middleware(['auth', 'role:seller'])->name('seller.panel');

Route::get('/admin/panel', function () {
    return view('admin.panel');
})->middleware(['auth', 'role:admin'])->name('admin.panel');

Route::get('/client/panel', function () {
    return view('client.panel');
})->middleware(['auth', 'role:user'])->name('client.panel'); 

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('sales', SaleController::class);
});

Route::middleware(['auth', 'role:seller'])->prefix('client')->name('seller.')->group(function () {});
Route::middleware(['auth'])->prefix('client')->name('seller.')->group(function () {

    Route::resource('artworks', SellerArtworkController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/seller/{seller}/follow', [SellerFollowController::class, 'follow'])->name('seller.follow');
    Route::delete('/seller/{seller}/unfollow', [SellerFollowController::class, 'unfollow'])->name('seller.unfollow');
});


Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('followers', [SellerFollowersController::class, 'index'])->name('followers.index');
    Route::get('sales', [\App\Http\Controllers\Seller\SellerSaleController::class, 'index'])->name('sales.index');
    Route::get('sales/{id}', [\App\Http\Controllers\Seller\SellerSaleController::class, 'show'])->name('sales.show');
    Route::delete('sales/{id}', [\App\Http\Controllers\Seller\SellerSaleController::class, 'destroy'])->name('sales.destroy');


    Route::get('finances', [FinanceController::class, 'index'])->name('finances.index');
    Route::post('finances/statement', [FinanceController::class, 'generateStatement'])->name('finances.statement');
    Route::get('finances/withdraw', [FinanceController::class, 'withdrawForm'])->name('finances.withdraw.form');
    Route::post('finances/withdraw', [FinanceController::class, 'processWithdrawal'])->name('finances.withdraw.process');

    // Istniejące trasy do zarządzania profilem sprzedawcy
    Route::get('description', [\App\Http\Controllers\Seller\SellerDescriptionController::class, 'edit'])->name('description.edit');
    Route::post('description', [\App\Http\Controllers\Seller\SellerDescriptionController::class, 'update'])->name('description.update');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Statystyki sprzedawcy
    Route::get('statystyki', [SellerStatisticsController::class, 'index'])->name('statistics.index');

    // Zarządzanie dziełami sztuki sprzedawcy
    Route::resource('artworks', SellerArtworkController::class);
});

Route::get('/', function () {
    $artworks = \App\Models\Artwork::where('is_priceless', 0)
                                     ->where('is_sold', false) 
                                     ->paginate(9);

    $cartArtworks = [];
    if (Auth::check() && Auth::user()->role === 'user') {
        $cart = Auth::user()->cart; 
        if ($cart) {
            $cart = $cart->load('items'); 
            $cartArtworks = $cart->items->pluck('artwork_id')->toArray();
        }
    }

    return view('home', compact('artworks', 'cartArtworks'));
})->name('home');

Route::get('/kolekcje', [KolekcjeController::class, 'index'])->name('kolekcje.index');

Route::get('/forgot-password', function () {
    return view('auth.resetPassword');
})->name('token.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('token.email');

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('purchases', [\App\Http\Controllers\Client\ClientPurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/{id}', [\App\Http\Controllers\Client\ClientPurchaseController::class, 'show'])->name('purchases.show');
        Route::resource('addresses', AddressController::class)->except(['show']);
        Route::get('wallet', [ClientWalletController::class, 'index'])->name('wallet.index');
        Route::get('wallet/topup', [ClientWalletController::class, 'showTopUpForm'])->name('wallet.topup.form');
        Route::post('wallet/topup', [ClientWalletController::class, 'processTopUp'])->name('wallet.topup.process');
    });
});

Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/followers', [\App\Http\Controllers\Client\FollowersController::class, 'index'])->name('followers.index');
});

Route::get('/followed-offers-section', function () {
    $artworks = \App\Models\Artwork::whereIn('user_id', auth()->user()->followedSellers->pluck('id'))
                                     ->where('is_priceless', 0)
                                     ->where('is_sold', false) 
                                     ->get();
    return view('client.followers.followed_offers', compact('artworks'))->render();
})->middleware('auth')->name('client.followed.offers');


Route::middleware(['auth', 'role:user'])->prefix('client')->name('client.')->group(function () {
    // Koszyk
    Route::get('cart', [\App\Http\Controllers\Client\CartController::class, 'index'])->name('cart.index');
    Route::post('cart/add/{id}', [\App\Http\Controllers\Client\CartController::class, 'add'])->name('cart.add');
    Route::delete('cart/remove/{id}', [\App\Http\Controllers\Client\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('cart/clear', [\App\Http\Controllers\Client\CartController::class, 'clear'])->name('cart.clear');

    // Nowe trasy do checkoutu
    Route::get('cart/checkout', [\App\Http\Controllers\Client\CartController::class, 'checkoutForm'])->name('cart.checkout.form');
    Route::post('cart/checkout/submit', [\App\Http\Controllers\Client\CartController::class, 'checkout'])->name('cart.checkout');

    // Zamówienia klienta
    Route::post('orders', [\App\Http\Controllers\Client\OrderController::class, 'store'])->name('orders.store');
    Route::get('orders', [\App\Http\Controllers\Client\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Client\OrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'role:user'])->prefix('client')->name('client.')->group(function () {
    Route::get('donations', [\App\Http\Controllers\Client\DonationController::class, 'index'])->name('donations.index');
    Route::post('donations', [\App\Http\Controllers\Client\DonationController::class, 'store'])->name('donations.store');
});

Route::middleware(['auth', 'role:user'])->get('/client/followers/followed-offers', [\App\Http\Controllers\Client\FollowersController::class, 'followedOffers'])->name('client.followers.followed_offers');

Route::get('/artworks', [ArtworkController::class, 'index'])->name('artworks.index');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.manage.users.')->group(function () {
    Route::get('users', [UsersController::class, 'index'])->name('index');
    Route::get('users/create', [UsersController::class, 'create'])->name('create');
    Route::post('users', [UsersController::class, 'store'])->name('store');
    Route::get('users/{user}', [UsersController::class, 'show'])->name('show');
    Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('edit');
    Route::put('users/{user}', [UsersController::class, 'update'])->name('update');
    Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.manage.sellers.')->group(function () {
    Route::get('sellers', [\App\Http\Controllers\Admin\SellersController::class, 'index'])->name('index');
    Route::get('sellers/create', [\App\Http\Controllers\Admin\SellersController::class, 'create'])->name('create');
    Route::post('sellers', [\App\Http\Controllers\Admin\SellersController::class, 'store'])->name('store');
    Route::get('sellers/{user}', [\App\Http\Controllers\Admin\SellersController::class, 'show'])->name('show');
    Route::get('sellers/{user}/edit', [\App\Http\Controllers\Admin\SellersController::class, 'edit'])->name('edit');
    Route::put('sellers/{user}', [\App\Http\Controllers\Admin\SellersController::class, 'update'])->name('update');
    Route::delete('sellers/{user}', [\App\Http\Controllers\Admin\SellersController::class, 'destroy'])->name('destroy');
});


Route::get('/client/statistics', [\App\Http\Controllers\Client\StatisticsController::class, 'index'])->middleware(['auth', 'role:user'])->name('client.statistics.index'); 

Route::get('/client/statystyki', [StatisticsController::class, 'index'])
    ->middleware(['auth', 'role:user']) 
    ->name('client.statistics.index');

Route::get('/seller/statystyki', [SellerStatisticsController::class, 'index'])
    ->middleware(['auth', 'role:seller'])
    ->name('seller.statistics.index');

Route::middleware(['auth', 'role:admin'])->get('/admin/statystyki', [AdminStatisticsController::class, 'index'])
    ->name('admin.statistics.index');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('addresses', AdminAddressController::class);
    Route::resource('categories', CategoryController::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('artworks', AdminArtworkController::class);
});


Route::get('/artworks', [ArtworkController::class, 'listPublic'])->name('artworks.index');


Route::middleware(['auth', 'role:user'])->prefix('client')->name('client.')->group(function () {
    Route::get('manage/update', function () {
        return view('client.manage.update', ['user' => auth()->user()]);
    })->name('manage.update');
    Route::put('manage/update', [\App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/profile/edit', function () {
        return view('admin.manage.self.update', ['user' => Auth::user()]);
    })->name('admin.profile.edit');

    Route::put('/admin/profile/update', [SelfAdminController::class, 'update'])->name('admin.profile.update');
});


Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller/profile/edit', [ProfileController::class, 'edit'])->name('seller.profile.edit');
    Route::put('/seller/profile/update', [ProfileController::class, 'update'])->name('seller.profile.update');
});

Route::get('/sellers', [SellerController::class, 'sellersIndex'])->name('guests.sellersIndex');
Route::get('/seller/{id}/profile', [SellerController::class, 'publicProfile'])->name('seller.profile.public');
Route::get('/seller/{id}/artworks', [SellerController::class, 'publicArtworks'])->name('seller.artworks.public');


Route::prefix('test-errors')->group(function () {
    Route::get('400', function () {
        abort(400);
    });
    Route::get('401', function () {
        abort(401);
    });
    Route::get('403', function () {
        abort(403);
    });
    Route::get('404', function () {
        abort(404);
    });
    Route::get('500', function () {
        abort(500);
    });
    Route::get('503', function () {
        abort(503);
    });
});
