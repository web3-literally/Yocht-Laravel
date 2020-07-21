<?php
# Shop (Store)
/*Route::group($langRouteGroup, function () {
    Route::group(['prefix' => 'store', 'as' => 'store.'], function () {
        Route::get('/', 'StoreController@index')->name('index');
        Route::group(['middleware' => 'user'], function () {
            Route::get('cart', 'StoreController@showCart')->name('cart');
            Route::get('checkout', 'StoreController@showCheckout')->name('checkout');
        });
        Route::get('checkout/order/{order}', 'StoreController@showCheckoutOrder')->name('checkout.order');
        Route::get('{slug}', 'StoreController@showProduct')->name('product');
    });
});
Route::group(['prefix' => 'store', 'as' => 'store.'], function () {
    Route::group(['middleware' => 'user'], function () {
        Route::post('cart/add/{id}', 'StoreController@addProductToCart')->name('cart.item.add');
        Route::get('cart/remove/{id}', 'StoreController@removeProductFromCart')->name('cart.item.remove');
        Route::post('checkout/place-order', 'StoreController@placeOrder')->name('checkout.place-order');
    });
    Route::get('callback/payment/{status}/{id}/{shoptoken}', 'StoreCallbackController@callback')->name('callback');
    Route::post('callback/payment/{status}/{id}/{shoptoken}', 'StoreCallbackController@callback');
});*/