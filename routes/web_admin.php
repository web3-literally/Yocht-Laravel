<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Admin site routes /////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

# Auth admin panel routes (guest access)
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    # Error pages should be shown without requiring login
    Route::get('404', function () {
        return view('admin/404');
    });
    Route::get('500', function () {
        return view('admin/500');
    });

    # Lock screen
    Route::get('{id}/lockscreen', 'UsersController@lockscreen')->name('lockscreen');
    Route::post('{id}/lockscreen', 'UsersController@postLockscreen')->name('lockscreen');

    # All basic routes defined here
    Route::get('signin', 'AuthController@getSignin')->name('admin.signin');
    Route::post('signin', 'AuthController@postSignin')->name('admin.signin.store');
    # Signup/Activation for admin panel is not available
    // Route::post('signup', 'AuthController@postSignup')->name('admin.signup');
    // Route::get('activate/{userId}/{activationCode}', 'AuthController@getActivate')->name('activate');
    Route::post('forgot-password', 'AuthController@postForgotPassword')->name('forgot-password');

    # Forgot Password Confirmation
    // Route::get('forgot-password/{userId}/{passwordResetCode}', 'AuthController@getForgotPasswordConfirm')->name('forgot-password-confirm');
    // Route::post('forgot-password/{userId}/{passwordResetCode}', 'AuthController@getForgotPasswordConfirm');

    # Logout
    Route::get('logout', 'AuthController@getLogout')->name('logout');
});

# Admin panel routes (user access)
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'backend', 'as' => 'admin.'], function () {
    # Dashboard
    Route::get('/', 'BackEndController@showHome')->name('dashboard');

    # Menu
    Route::group(['prefix' => 'menus', 'middleware' => 'check-permission:admin.menus', 'as' => 'menus.'], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Menu\MenuController@index']);
        Route::get('create', ['as' => 'create', 'uses' => 'Menu\MenuController@create']);
        Route::post('store', ['as' => 'store', 'uses' => 'Menu\MenuController@store']);
        Route::get('edit/{id}/structure', ['as' => 'edit.structure', 'uses' => 'Menu\MenuController@editStructure']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'Menu\MenuController@edit']);
        Route::patch('update/{id}', ['as' => 'update', 'uses' => 'Menu\MenuController@update']);
        Route::get('delete/{id}', ['as' => 'confirm-delete', 'uses' => 'Menu\MenuController@delete']);
        Route::get('get/{menu}/tree', ['as' => 'get.tree', 'uses' => 'Menu\MenuController@treeStructure']);
        Route::get('item/edit', ['as' => 'item.edit', 'uses' => 'Menu\MenuItemController@edit']);
        Route::patch('item/update/{id}', ['as' => 'item.update', 'uses' => 'Menu\MenuItemController@update']);
        Route::post('item/store', ['as' => 'item.store', 'uses' => 'Menu\MenuItemController@store']);
        Route::post('item/rename', ['as' => 'item.rename', 'uses' => 'Menu\MenuItemController@rename']);
        Route::post('item/delete', ['as' => 'item.delete', 'uses' => 'Menu\MenuItemController@delete']);
        Route::post('item/move', ['as' => 'item.move', 'uses' => 'Menu\MenuItemController@move']);
    });

    # Pages
    Route::group(['prefix' => 'pages', 'middleware' => 'check-permission:admin.pages'], function () {
        Route::post('upload', 'PagesController@upload')->name('pages.upload');
        Route::get('data', 'PagesController@data')->name('pages.data');
        Route::get('{page}/delete', 'PagesController@destroy')->name('pages.delete');
        Route::get('{page}/confirm-delete', 'PagesController@getModalDelete')->name('pages.confirm-delete');
    });
    Route::group(['middleware' => 'check-permission:admin.pages'], function() {
        Route::resource('pages', 'PagesController');
    });

    # News
    Route::group(['prefix' => 'news', 'middleware' => 'check-permission:admin.news', 'as' => 'news'], function () {
        Route::get('/', ['as'=> '.index', 'uses' => 'News\NewsController@index']);
        Route::post('news', ['as'=> '.store', 'uses' => 'News\NewsController@store']);
        Route::get('create', ['as'=> '.create', 'uses' => 'News\NewsController@create']);
        Route::get('{news}/edit', ['as'=> '.edit', 'uses' => 'News\NewsController@edit']);
        Route::patch('{news}/update', ['as'=> '.update', 'uses' => 'News\NewsController@update']);
        Route::get('{id}/delete', array('as' => '.delete', 'uses' => 'News\NewsController@getDelete'));
        Route::get('image/delete/{id}', ['as' => '.image.delete', 'uses' => 'News\NewsController@deleteImage']);
        Route::get('{id}/confirm-delete', array('as' => '.confirm-delete', 'uses' => 'News\NewsController@getModalDelete'));
        Route::get('data', 'News\NewsController@data')->name('.data');

        # Sources
        Route::group(['prefix' => 'sources', 'middleware' => 'check-permission:admin.news.sources', 'as' => '.sources'], function () {
            Route::get('/', ['as' => '.index', 'uses' => 'News\NewsSourceController@index']);
            Route::post('store', ['as'=> '.store', 'uses' => 'News\NewsSourceController@store']);
            Route::get('create', ['as'=> '.create', 'uses' => 'News\NewsSourceController@create']);
            Route::get('{sources}/edit', ['as'=> '.edit', 'uses' => 'News\NewsSourceController@edit']);
            Route::patch('{sources}/update', ['as'=> '.update', 'uses' => 'News\NewsSourceController@update']);
            Route::get('{id}/delete', array('as' => '.delete', 'uses' => 'News\NewsSourceController@getDelete'));
            Route::get('{id}/confirm-delete', array('as' => '.confirm-delete', 'uses' => 'News\NewsSourceController@getModalDelete'));
        });
    });

    # Blog
    Route::group(['prefix' => 'blog', 'middleware' => 'check-permission:admin.blog'], function () {
        Route::post('upload', 'BlogController@upload')->name('blog.upload');
        Route::get('data', 'BlogController@data')->name('blog.data');
        Route::get('{blog}/delete', 'BlogController@destroy')->name('blog.delete');
        Route::get('{blog}/confirm-delete', 'BlogController@getModalDelete')->name('blog.confirm-delete');
        Route::get('{blog}/restore', 'BlogController@restore')->name('blog.restore');
        Route::post('{blog}/storecomment', 'BlogController@storeComment')->name('storeComment');
    });
    Route::group(['middleware' => 'check-permission:admin.blog'], function() {
        Route::resource('blog', 'BlogController');
    });
    Route::group(['prefix' => 'blogcategory', 'middleware' => 'check-permission:admin.blog'], function () {
        Route::get('data', 'BlogCategoryController@data')->name('blogcategory.data');
        Route::get('{blogCategory}/delete', 'BlogCategoryController@destroy')->name('blogcategory.delete');
        Route::get('{blogCategory}/confirm-delete', 'BlogCategoryController@getModalDelete')->name('blogcategory.confirm-delete');
        Route::get('{blogCategory}/restore', 'BlogCategoryController@getRestore')->name('blogcategory.restore');
    });
    Route::group(['middleware' => 'check-permission:admin.blog'], function() {
        Route::resource('blogcategory', 'BlogCategoryController');
    });

    # Events
    Route::group(['prefix' => 'events', 'middleware' => 'check-permission:admin.events'], function () {
        Route::get('/', 'Events\EventsController@index')->name('events.index');
        Route::get('form', 'Events\EventsController@form')->name('events.form');
        Route::get('data', 'Events\EventsController@data')->name('events.data');
        Route::patch('store', 'Events\EventsController@store')->name('events.store');
        Route::patch('update/{id}', 'Events\EventsController@update')->name('events.update');
        Route::get('delete/{id}', 'Events\EventsController@delete')->name('events.delete');

        Route::get('categories', 'Events\EventsCategoryController@index')->name('events.categories');
        Route::get('categories/create', 'Events\EventsCategoryController@create')->name('events.categories.create');
        Route::patch('categories/store', 'Events\EventsCategoryController@store')->name('events.categories.store');
        Route::get('categories/edit/{id}', 'Events\EventsCategoryController@edit')->name('events.categories.edit');
        Route::patch('categories/update/{id}', 'Events\EventsCategoryController@update')->name('events.categories.update');
        Route::get('categories/delete/{id}', 'Events\EventsCategoryController@destroy')->name('events.categories.delete');
    });

    # User Management
    Route::group(['prefix' => 'users', 'middleware' => 'check-permission:admin.users'], function () {
        Route::get('data', 'UsersController@data')->name('users.data');
        Route::get('deleted_data', 'UsersController@deleted_data')->name('users.deleted_data');
        Route::get('{user}/delete', 'UsersController@destroy')->name('users.delete');
        Route::get('{user}/confirm-delete', 'UsersController@getModalDelete')->name('users.confirm-delete');
        Route::get('{user}/restore', 'UsersController@getRestore')->name('restore.user');
        // Route::post('{user}/passwordreset', 'UsersController@passwordreset')->name('passwordreset');
        Route::post('passwordreset', 'UsersController@passwordreset')->name('passwordreset');
    });
    Route::group(['middleware' => 'check-permission:admin.users'], function() {
        Route::resource('users', 'UsersController');
        Route::get('deleted_users', ['before' => 'Sentinel', 'uses' => 'UsersController@getDeletedUsers'])->name('deleted_users');
    });

    Route::group(['middleware' => 'check-permission:admin.users'], function() {
        # User Positions
        Route::get('positions', ['as' => 'positions.index', 'uses' => 'PositionController@index']);
        Route::post('positions', ['as' => 'positions.store', 'uses' => 'PositionController@store']);
        Route::get('positions/create', ['as' => 'positions.create', 'uses' => 'PositionController@create']);
        Route::put('positions/{positions}', ['as' => 'positions.update', 'uses' => 'PositionController@update']);
        Route::patch('positions/{positions}', ['as' => 'positions.update', 'uses' => 'PositionController@update']);
        Route::get('positions/{id}/delete', array('as' => 'positions.delete', 'uses' => 'PositionController@getDelete'));
        Route::get('positions/{id}/confirm-delete', array('as' => 'positions.confirm-delete', 'uses' => 'PositionController@getModalDelete'));
        Route::get('positions/{positions}/edit', ['as' => 'positions.edit', 'uses' => 'PositionController@edit']);

        # User Specializations
        Route::get('specializations', ['as' => 'specializations.index', 'uses' => 'SpecializationController@index']);
        Route::post('specializations', ['as' => 'specializations.store', 'uses' => 'SpecializationController@store']);
        Route::get('specializations/create', ['as' => 'specializations.create', 'uses' => 'SpecializationController@create']);
        Route::put('specializations/{specializations}', ['as' => 'specializations.update', 'uses' => 'SpecializationController@update']);
        Route::patch('specializations/{specializations}', ['as' => 'specializations.update', 'uses' => 'SpecializationController@update']);
        Route::get('specializations/{id}/delete', array('as' => 'specializations.delete', 'uses' => 'SpecializationController@getDelete'));
        Route::get('specializations/{id}/confirm-delete', array('as' => 'specializations.confirm-delete', 'uses' => 'SpecializationController@getModalDelete'));
        Route::get('specializations/{specializations}/edit', ['as' => 'specializations.edit', 'uses' => 'SpecializationController@edit']);
    });

    # Group Management
    Route::group(['prefix' => 'groups', 'middleware' => 'check-permission:admin.users.groups'], function () {
        Route::get('{group}/delete', 'GroupsController@destroy')->name('groups.delete');
        Route::get('{group}/confirm-delete', 'GroupsController@getModalDelete')->name('groups.confirm-delete');
        Route::get('{group}/restore', 'GroupsController@getRestore')->name('groups.restore');
    });
    Route::group(['middleware' => 'check-permission:admin.users.groups'], function() {
        Route::resource('groups', 'GroupsController');
    });

    # Jobs
    Route::group(['prefix' => 'jobs', 'middleware' => 'check-permission:admin.jobs', 'as' => 'jobs.'], function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Jobs\JobsController@index']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'Jobs\JobsController@edit']);
        Route::patch('update/{id}', ['as' => 'update', 'uses' => 'Jobs\JobsController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'Jobs\JobsController@delete']);
        Route::get('image/delete/{id}', ['as' => 'image.delete', 'uses' => 'Jobs\JobsController@deleteImage']);
        Route::get('data', ['as' => 'data', 'uses' => 'Jobs\JobsController@data']);
    });

    # Classifieds
    Route::group(array('prefix' => 'classifieds', 'middleware' => 'check-permission:admin.classifieds', 'as' => 'classifieds.'), function () {
        Route::get('index', ['as' => 'index', 'uses' => 'Classifieds\ClassifiedsController@index']);
        Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'Classifieds\ClassifiedsController@edit']);
        Route::patch('update/{id}', ['as' => 'update', 'uses' => 'Classifieds\ClassifiedsController@update']);
        Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'Classifieds\ClassifiedsController@delete']);
        Route::get('delete/{id}/image/{image}', ['as' => 'delete-image', 'uses' => 'Classifieds\ClassifiedsController@deleteImage']);
        Route::get('data', ['as' => 'data', 'uses' => 'Classifieds\ClassifiedsController@data']);
    });

    # Vessels
    Route::group(array('prefix' => 'vessels', 'middleware' => 'check-permission:admin.vessels', 'as' => 'vessels.'), function () {
        Route::group(array('prefix' => 'manufacturers', 'as' => 'manufacturers.'), function () {
            Route::get('/', ['as'=> 'index', 'uses' => 'VesselManufacturerController@index']);
            Route::get('create', ['as'=> 'create', 'uses' => 'VesselManufacturerController@create']);
            Route::post('store', ['as'=> 'store', 'uses' => 'VesselManufacturerController@store']);
            Route::get('{id}/edit', ['as'=> 'edit', 'uses' => 'VesselManufacturerController@edit']);
            Route::patch('{id}/update', ['as'=> 'update', 'uses' => 'VesselManufacturerController@update']);
            Route::get('{id}/delete', array('as' => 'delete', 'uses' => 'VesselManufacturerController@getDelete'));
            Route::get('{id}/confirm-delete', array('as' => 'confirm-delete', 'uses' => 'VesselManufacturerController@getModalDelete'));
        });
    });

    # Services
    Route::group(array('prefix' => 'services', 'middleware' => 'check-permission:admin.services', 'as' => 'services.'), function () {
        Route::group(array('prefix' => 'categories', 'as' => 'categories.'), function () {
            Route::get('index', ['as'=> 'index', 'uses' => 'Services\ServiceCategoryController@index']);
            Route::get('create', ['as'=> 'create', 'uses' => 'Services\ServiceCategoryController@create']);
            Route::post('store', ['as'=> 'store', 'uses' => 'Services\ServiceCategoryController@store']);
            Route::get('{id}/edit', ['as'=> 'edit', 'uses' => 'Services\ServiceCategoryController@edit']);
            Route::patch('{id}/update', ['as'=> 'update', 'uses' => 'Services\ServiceCategoryController@update']);
            Route::get('{id}/delete', array('as' => 'delete', 'uses' => 'Services\ServiceCategoryController@getDelete'));
            Route::get('{id}/confirm-delete', array('as' => 'confirm-delete', 'uses' => 'Services\ServiceCategoryController@getModalDelete'));
        });
        Route::get('index', ['as'=> 'index', 'uses' => 'Services\ServiceController@index']);
        Route::get('create', ['as'=> 'create', 'uses' => 'Services\ServiceController@create']);
        Route::post('store', ['as'=> 'store', 'uses' => 'Services\ServiceController@store']);
        Route::get('{id}/edit', ['as'=> 'edit', 'uses' => 'Services\ServiceController@edit']);
        Route::patch('{id}/update', ['as'=> 'update', 'uses' => 'Services\ServiceController@update']);
        Route::get('{id}/delete', array('as' => 'delete', 'uses' => 'Services\ServiceController@getDelete'));
        Route::get('{id}/confirm-delete', array('as' => 'confirm-delete', 'uses' => 'Services\ServiceController@getModalDelete'));
    });

    # Shop
    /*Route::group(['prefix' => 'shop', 'middleware' => 'check-permission:shop', 'as' => 'shop.'], function () {
        # Products
        Route::get('products', ['as' => 'products.index', 'uses' => 'Shop\ProductsController@index']);
        Route::post('products', ['as' => 'products.store', 'uses' => 'Shop\ProductsController@store']);
        Route::get('products/create', ['as' => 'products.create', 'uses' => 'Shop\ProductsController@create']);
        Route::put('products/{products}', ['as' => 'products.update', 'uses' => 'Shop\ProductsController@update']);
        Route::patch('products/{products}', ['as' => 'products.update', 'uses' => 'Shop\ProductsController@update']);
        Route::get('products/{id}/delete', array('as' => 'products.delete', 'uses' => 'Shop\ProductsController@getDelete'));
        Route::get('products/{id}/confirm-delete', array('as' => 'products.confirm-delete', 'uses' => 'Shop\ProductsController@getModalDelete'));
        Route::get('products/{products}/edit', ['as' => 'products.edit', 'uses' => 'Shop\ProductsController@edit']);
        Route::get('products/data', ['as' => 'products.data', 'uses' => 'Shop\ProductsController@data']);
        # Orders
        Route::get('orders', ['as' => 'orders.index', 'uses' => 'Shop\OrdersController@index']);
        Route::get('orders/{id}/edit', ['as' => 'orders.edit', 'uses' => 'Shop\OrdersController@edit']);
        Route::patch('orders/update/{id}/status', ['as' => 'orders.update.status', 'uses' => 'Shop\OrdersController@updateStatus']);
        Route::get('orders/data', ['as' => 'orders.data', 'uses' => 'Shop\OrdersController@data']);
    });*/

    # Reviews
    Route::group(array('prefix' => 'reviews', 'middleware' => 'check-permission:admin.reviews', 'as' => 'reviews.'), function () {
        Route::get('/', ['as'=> 'index', 'uses' => 'ReviewController@index']);
        Route::get('reviews/{id}/edit', ['as'=> 'edit', 'uses' => 'ReviewController@edit']);
        Route::patch('reviews/{id}/update', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'as'=> 'update', 'uses' => 'ReviewController@update']);
        Route::get('data', ['as' => 'data', 'uses' => 'ReviewController@data']);
        Route::post('set-status', ['as' => 'set-status', 'uses' => 'ReviewController@setStatus']);
    });

    # Billing
    Route::group(['prefix' => 'billing', 'middleware' => 'check-permission:admin.billing'], function () {
        Route::get('plans', 'BillingController@plans')->name('billing.plans');
        Route::get('subscriptions', 'BillingController@subscriptions')->name('billing.subscriptions');
        Route::get('subscriptions/data', 'BillingController@subscriptionsData')->name('billing.subscriptions.data');
        Route::get('subscriptions/data/user/{user}', 'BillingController@subscriptionsUserData')->name('billing.subscriptions.user.data');
        Route::get('subscriptions/{id}/cancel/now', 'BillingController@subscriptionCancelNow')->name('billing.subscriptions.cancel');
    });

    # GUI Crud Generator
    Route::group(['middleware' => 'check-permission:dev'], function () {
        Route::get('generator_builder', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@builder')->name('generator_builder');
        Route::get('field_template', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@fieldTemplate');
        Route::post('generator_builder/generate', '\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController@generate');
        // Model checking
        Route::post('modelCheck', 'ModelcheckController@modelCheck');
    });

    # Log Viewer
    Route::group(['middleware' => 'check-permission:dev'], function () {
        Route::get('log_viewers', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@index')->name('log-viewers');
        Route::get('log_viewers/logs', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@listLogs')->name('log_viewers.logs');
        Route::delete('log_viewers/logs/delete', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@delete')->name('log_viewers.logs.delete');
        Route::get('log_viewers/logs/{date}', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@show')->name('log_viewers.logs.show');
        Route::get('log_viewers/logs/{date}/download', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@download')->name('log_viewers.logs.download');
        Route::get('log_viewers/logs/{date}/{level}', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@showByLevel')->name('log_viewers.logs.filter');
        Route::get('log_viewers/logs/{date}/{level}/search', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@search')->name('log_viewers.logs.search');
        Route::get('log_viewers/logcheck', '\Arcanedev\LogViewer\Http\Controllers\LogViewerController@logCheck')->name('log-viewers.logcheck');
    });

    # Activity Log
    Route::group(['middleware' => 'check-permission:audit'], function () {
        Route::get('activity_log', 'ActivityLogController@index')->name('activity_log.index');
        Route::get('activity_log/data', 'ActivityLogController@activityLogData')->name('activity_log.data');
    });

    # Uploads
    /*Route::group(['prefix' => 'file'], function () {
        Route::post('create', 'FileController@store');
        Route::post('createmulti', 'FileController@postFilesCreate')->name('postFilesCreate');
        Route::delete('delete', 'FileController@delete')->name('delete');
        Route::get('{id}/delete', 'FileController@destroy')->name('file.delete');
        Route::get('data', 'FileController@data')->name('file.data');
        Route::get('{user}/delete', 'FileController@destroy')->name('users.delete');

    });*/

    # Sortable
    Route::post('sort', '\Rutorika\Sortable\SortableController@sort')->name('sort');
});