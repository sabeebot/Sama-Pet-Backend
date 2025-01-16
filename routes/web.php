<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Contract\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\ProviderAllController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/testing-from', function () {
    return view('testing');
});

Route::get('/send_provider', function () {
    return view('send_provider');
});
Route::resource('/provider_all', ProviderAllController::class);
Route::post('/provider_all/store', [ProviderAllController::class, 'store'])->name('provider_all.store');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test1', function () {
    $storage = app('firebase.storage');
    $bucket = $storage->getBucket();
    $url = $bucket->object('stk2.png')->signedUrl(new DateTime('+1 day'));
    return $url;
});
