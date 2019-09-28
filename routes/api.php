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

// default index request response
Route::get('/', function () {
    return 'Petstore RESTful API';
});

// setup api version 1 routes
Route::prefix('v1')->group(function () {
    // Create new pet object
    Route::post('pet', 'API\PetController@create');

    // Retrieve pet object by id
    Route::get('pet/{id}', 'API\PetController@read');

    // Delete pet object by id
    Route::delete('pet/{id}', 'API\PetController@delete');
});
