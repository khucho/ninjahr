<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebAuthnRegisterController;
use App\Http\Controllers\Auth\WebAuthnLoginController;
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



Auth::routes(['register' => false]);

Route::post('webauthn/register/options', [WebAuthnRegisterController::class, 'options'])
     ->name('webauthn.register.options');
Route::post('webauthn/register', [WebAuthnRegisterController::class, 'register'])
     ->name('webauthn.register');

Route::post('webauthn/login/options', [WebAuthnLoginController::class, 'options'])
     ->name('webauthn.login.options');
Route::post('webauthn/login', [WebAuthnLoginController::class, 'login'])
     ->name('webauthn.login');

Route::middleware('auth')->group(function(){
    Route::get('/','PageController@home')->name('home');

    # Employee
    Route::resource('employee','EmployeeController');
    // Datatable
    Route::get('employee/datatable/ssd','EmployeeController@ssd');

    # Department
    Route::resource('department','DepartmentController');
    // Datatable
    Route::get('department/datatable/ssd','DepartmentController@ssd');

    # Role
    Route::resource('role','RoleController');
    // Datatable
    Route::get('role/datatable/ssd','RoleController@ssd');

    # Permission
    Route::resource('permission','PermissionController');
    // Datatable
    Route::get('permission/datatable/ssd','PermissionController@ssd');

    # Company Setting
    Route::resource('company_setting','CompanySettingController')->only(['edit','update','show']);

    Route::get('profile','ProfileController@profile')->name('profile.profile');
});
