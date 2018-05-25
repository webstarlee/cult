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

Route::post('login', 'Api\ApiAuthController@login');
Route::post('register', 'Api\ApiAuthController@register');

Route::get('sendautherror', function(){
    return response()->json(['result' => 'error', 'msg' => 'Unauthenticated']);
});

Route::group(['middleware' => 'auth:api'], function(){
    Route::get('check', 'Api\ApiAuthController@details');
    Route::prefix('profile')->group(function () {
        Route::post('/update/photo', 'Api\ApiUserController@updatePhoto');
    });
    Route::post('find_friend', 'Api\ApiUserController@findFriend');
    Route::prefix('cult')->group(function () {
        Route::post('/new', 'Api\ApiUserController@newCult');
        Route::get('/all', 'Api\ApiUserController@fetAllCult');
    });
    Route::prefix('friend')->group(function () {
        Route::post('/request', 'Api\ApiUserController@friendRequest');
        Route::post('/request/delete', 'Api\ApiUserController@friendRequestDelete');
    });
});
