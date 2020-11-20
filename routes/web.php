<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Voyager\DonaturController;
use App\Http\Controllers\Voyager\VoyagerUserController;
use App\Http\Controllers\Voyager\ProgramGroupsController;

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

// Route::get('/', function () {



Route::group(['prefix' => 'admin'], function () {

    Route::get('donaturs/add-donation','Voyager\DonaturController@add_donation')->name('donaturs.add_donation');
    Route::post('donaturs/store-donation','Voyager\DonaturController@store_donation')->name('donaturs.store_donation');
    Route::get('donaturs/donation-history/{donatur_id}','Voyager\DonaturController@donation_history_index')->name('donaturs.donation_history');
    Route::get('domisili/get-json','DomisiliController@get_json')->name('domisili.get_json');
    Route::get('donaturs/print','Voyager\DonaturController@print')->name('donaturs.print');
    Route::get('donaturs/generate_and_print_last_month','Voyager\DonaturController@generate_and_print_last_month')->name('donaturs.generate_and_print_last_month');
    Route::post('konfirmasi-donasi','Voyager\DonaturController@confirm_donation')->name('donaturs.confirm_donation');
    Route::post('file-import', [ProgramGroupsController::class, 'fileImport'])->name('file-import');
    Route::post('file-import-users', [VoyagerUserController::class, 'fileImport'])->name('file-import-users');
    Route::post('file-import-donaturs', [DonaturController::class, 'fileImport'])->name('file-import-donaturs');
    Voyager::routes();
});
// });

// Route::group(['prefix' => 'admin/adminpusat'], function () {

//     Route::get('donaturs/add-donation','Voyager\DonaturController@add_donation')->name('donaturs.add_donation');
//     Route::post('donaturs/store-donation','Voyager\DonaturController@store_donation')->name('donaturs.store_donation');
//     Route::get('donaturs/donation-history/{donatur_id}','Voyager\DonaturController@donation_history_index')->name('donaturs.donation_history');
//     Route::get('domisili/get-json','DomisiliController@get_json')->name('domisili.get_json');
//     Route::get('donaturs/print','Voyager\DonaturController@print')->name('donaturs.print');
//     Route::get('donaturs/generate_and_print_last_month','Voyager\DonaturController@generate_and_print_last_month')->name('donaturs.generate_and_print_last_month');
//     Route::post('konfirmasi-donasi','Voyager\DonaturController@confirm_donation')->name('donaturs.confirm_donation');
    
// });


Route::post('/import','DomisiliController@import');
