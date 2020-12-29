<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Voyager\DonaturController;
use App\Http\Controllers\Voyager\MidtranController;
use App\Http\Controllers\Voyager\VoyagerUserController;
use App\Http\Controllers\Voyager\ProgramGroupsController;
use App\Http\Controllers\Voyager\VoyagerCabangController;
use App\Http\Controllers\Voyager\VoyagerPetugasController;

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
Route::get('reset', function (){
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    
});
Route::get('/symlink_create', function () {
    Artisan::call('storage:link');
});

Route::group(['prefix' => 'admin'], function () {
    Route::get('donaturs/add-donation','Voyager\DonaturController@add_donation')->name('donaturs.add_donation');
    Route::post('donaturs/store-donation','Voyager\DonaturController@store_donation')->name('donaturs.store_donation');
    Route::get('donaturs/group.donaturs/{group_id}','Voyager\DonaturController@index')->name('voyager.donaturs.index.groups');
    Route::get('donaturs/group.donaturs.detail/{id}','Voyager\ProgramGroupsController@index')->name('voyager.donatur-groups.index.detail');
    Route::get('donaturs/donation-history/{donatur_id}','Voyager\DonaturController@donation_history_index')->name('donaturs.donation_history');
    Route::get('users/detail-sub-branch/{parent_id}','Voyager\VoyagerUserController@detailBranchUser')->name('users.sub.branch');
    Route::get('users/create','Voyager\VoyagerUserController@create')->name('voyager.users.create');
    Route::get('users/relation','Voyager\VoyagerUserController@relation')->name('voyager.users.relation');
    Route::get('users/{id}/edit','Voyager\VoyagerUserController@show')->name('users.sub.donaturgroups');
    Route::get('users/{id}','Voyager\VoyagerUserController@destroy')->name('users.sub.donaturgroups.destroy');
    Route::get('donaturs/donatur-groups/{group_id}','Voyager\VoyagerUserController@donaturDetailTransaction')->name('donaturs.sub.amil.history');
    Route::get('domisili/get-json','DomisiliController@get_json')->name('domisili.get_json');
    Route::get('donaturs/print','Voyager\DonaturController@prints')->name('donaturs.print');
    Route::get('donaturs/print/{cabang}','Voyager\DonaturController@print')->name('donaturs.print.prcabang');
    Route::get('donaturs/reset/{table_name}','Voyager\DonaturController@testResetIncrement')->name('donaturs.resets');
    Route::post('donaturs/generate_and_print_last_month','Voyager\DonaturController@generate_and_print_last_month')->name('donaturs.generate_and_print_last_month');
    Route::post('konfirmasi-donasi','Voyager\DonaturController@confirm_donation')->name('donaturs.confirm_donation');
    Route::post('file-import', [ProgramGroupsController::class, 'fileImport'])->name('file-import');
    Route::post('file-import-users-allow', [VoyagerUserController::class, 'import'])->name('file-import-users');
    Route::post('admin/users', [VoyagerUserController::class, 'store'])->name('voyager.users.store');
    Route::post('admin/users/{id}', [VoyagerUserController::class, 'update'])->name('voyager.users.update');
    Route::post('file-import-donaturs-attemps', [DonaturController::class, 'fileImport'])->name('file-import-donaturs');
    Route::post('file-import-branch', [VoyagerCabangController::class, 'fileImport'])->name('file-import-branch');
    Route::post('file-import-funding', [VoyagerPetugasController::class, 'fileImport'])->name('file-import-funding');
    Route::post('file-import-hisotry', [MidtranController::class, 'fileImport'])->name('file-import-history');
    Voyager::routes();
});
