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
    Route::get('payment-methods' , 'MainController@paymentMethods');
    Route::post('create-payment' , 'MainController@createPayment');

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
            Route::post('create-order' , 'MainController@CreateOrder');
            Route::get('order-details' , 'MainController@orderDetails');
            Route::get('new-orders' , 'MainController@newOrder');
            Route::get('current-orders' , 'MainController@currentOrder');
            Route::get('last-orders' , 'MainController@lastOrder');
            Route::post('register-notification-token' , 'AuthController@registerToken');
            Route::post('remove-notification-token' , 'AuthController@removeToken');
            Route::post('deliver-order' , 'MainController@deliverOrder');
            Route::post('decline-order' , 'MainController@declineOrder');
            Route::get('notification-list' , 'MainController@notificationList');
            Route::post('notification-update' , 'MainController@notificationUpdate');
        });
    });

    //
    Route::group(['namespace' => 'Resturant' ,  'prefix' => 'resturant'] , function (){

        // restaurant auth cycle
        Route::post('register' , 'AuthController@register');
        Route::post('login' , 'AuthController@login');
        Route::post('reset-password' , 'AuthController@resetPassword');
        Route::post('new-password' , 'AuthController@newPassword');
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
            Route::get('new-orders' , 'MainController@newOrder');
            Route::get('current-orders' , 'MainController@currentOrder');
            Route::get('last-orders' , 'MainController@lastOrder');
            Route::post('register-notification-token' , 'AuthController@registerToken');
            Route::post('remove-notification-token' , 'AuthController@removeToken');
            Route::post('accept-order' , 'MainController@acceptOrder');
            Route::post('reject-order' , 'MainController@rejectOrder');
            Route::get('notification-list' , 'MainController@notificationList');
            Route::post('notification-update' , 'MainController@notificationUpdate');
            Route::get('commission' , 'MainController@commission');
        });




    });




});

