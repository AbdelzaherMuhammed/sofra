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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api' , 'prefix' => 'v1'] , function (){
    Route::get('resturants' , 'MainController@resturants');
    Route::get('products' , 'MainController@products');
    Route::get('offers' , 'MainController@offers');
    Route::get('about-us' , 'MainController@about');
    Route::get('resturant-information' , 'MainController@resturant');
    Route::get('categories' , 'MainController@categories');

    // client auth cycle
    Route::group(['namespace' => 'Client' , 'prefix' => 'client'] , function (){
        Route::post('register', 'AuthController@register');
        Route::post('login' , 'AuthController@login');
        Route::post('reset-password' , 'AuthController@resetPassword');
        Route::post('new-password' , 'AuthController@newPassword');


        Route::group(['middleware' => 'auth:client-api'] , function (){
            Route::post('profile' , 'AuthController@profile');
            Route::post('change-password' , 'AuthController@changePassword');
            Route::post('client-contact' , 'MainController@contact');
            Route::post('add-review' , 'MainController@addReview');
        });
    });

    //
    Route::group(['namespace' => 'Resturant' ,  'prefix' => 'resturant'] , function (){

        // restaurant auth cycle
        Route::post('register' , 'AuthController@register');
        Route::post('login' , 'AuthController@login');
        Route::post('reset-password' , 'AuthController@resetPassword');
        Route::post('new-password' , 'AuthController@newPassword');


        Route::group(['middleware' => 'auth:resturant-api'] , function (){
            Route::post('profile' , 'AuthController@profile');
            Route::post('change-password' , 'AuthController@changePassword');
            Route::post('resturant-contact' , 'MainController@contact');
            Route::get('categories' , 'MainController@categories');
            Route::post('add-category' , 'MainController@addCategory');
            Route::post('edit-category' , 'MainController@editCategory');
            Route::post('delete-category' , 'MainController@deleteCategory');
            Route::get('products' , 'MainController@products');
            Route::post('add-product' , 'MainController@addProduct');
            Route::post('edit-product' , 'MainController@editProduct');
            Route::post('delete-product' , 'MainController@deleteProduct');
            Route::get('offers' , 'MainController@offers');
            Route::post('add-offer' , 'MainController@addOffer');
            Route::post('edit-offer' , 'MainController@editOffer');
            Route::post('delete-offer' , 'MainController@deleteOffer');
            Route::get('reviews' , 'MainController@reviews');


        });




    });




});

