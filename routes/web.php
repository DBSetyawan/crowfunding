<?php

use Illuminate\Support\Facades\Route;

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

/*home*/
Route::get('/', 'PageController@index');

Route::get('/cari','PageController@search')->name('search');





/*donation history */
Route::middleware(['auth'])->group(function () {
    Route::get('/donations','PageController@donations')->name('donation');
});
/*authentication */
// Route::get('')

Route::get('/login','AuthController@login')->name('auth.login');
Route::post('/login-post','AuthController@login_post')->name('auth.login_post');
Route::get('/register','AuthController@register')->name('auth.register');
Route::post('/register-post','AuthController@register_post')->name('auth.register_post');
Route::get('/register/get-domisili-json','AuthController@get_domisili_json')->name('auth.get_domisili_json');
Route::get('/profile/logout','AuthController@logout')->name('auth.logout')->middleware('auth');

/*profile */
Route::middleware(['auth'])->group(function () {
    Route::get('/profile','AuthController@profile')->name('profile');
    Route::get('/profile/edit','AuthController@profile_edit')->name('profile.edit');
    Route::post('/profile/edit-post','AuthController@profile_edit_post')->name('profile.edit_post');
});


Route::post('/midtrans-notification-handler', 'PaymentController@notificationHandler')->name('payment.notificationHandler');
 
/*programs and donate program */
Route::get('/{tipe}/{id}','PageController@program_detail')->name('program.detail');

Route::middleware(['auth'])->group(function () {
    Route::get('/{tipe}/{id}/donasi-sekarang','PaymentController@start_payment')->name('payment.start_payment');
    Route::get('/program/donasi-sekarang/get-snap','PaymentController@get_snap_token')->name('payment.get_snaptoken');
});



