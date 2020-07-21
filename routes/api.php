<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['not-for-production', 'api-qa'])->get('user', function (Request $request) {
    $email = request('email');

    $credentials = [
        'login' => $email,
    ];
    if (!empty($email) && $user = Sentinel::findByCredentials($credentials)) {
        if ($user->isFreeAccount() || $user->isMemberAccount()) {
            $user->roles;
            $user->profile;
            $user->position;
            $user->specialization;
            return $user;
        }
    }

    abort(404);
});

// Location
Route::group(['prefix' => 'geo'], function () {
    Route::get('location/search', 'Api\GeoController@searchLocation')->name('geo.location.search');
});

// Cities
\Igaster\LaravelCities\Geo::ApiRoutes();
Route::group(['prefix' => 'geo'], function () {
    Route::get('city/search', 'Api\GeoController@searchCity')->name('geo.city.search');
});

// Manufacturers
Route::group(['prefix' => 'manufacturers', 'as' => 'manufacturers.'], function () {
    Route::get('search', 'Api\ManufacturersController@search')->name('search');
});

// Services (Specializations)
Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
    Route::get('group', 'Api\ServicesController@group')->name('group');
    Route::get('category', 'Api\ServicesController@category')->name('category');
    Route::get('tree/all', ['as' => 'tree.all', 'uses' => 'Api\ServicesController@treeAll']);
});
