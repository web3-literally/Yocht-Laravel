<?php

Route::pattern('slug', '[a-z0-9- _]+');

include_once 'web_builder.php';

include_once 'web_admin.php';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Frontend site routes ////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$defaultRouteGroup = [
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
];

Route::group($defaultRouteGroup, function () {
    # Home
    Route::get('/', ['as' => 'home', function () {
        return view('index');
    }]);

    # Site Search
    Route::get('search', 'SearchController@search')->name('search');
    Route::get('quick/search', 'SearchController@quickSearch')->name('quick.search');

    # Dashboard
    Route::group(['middleware' => [\App\Http\Middleware\BackendUserRedirect::class, 'user']], function () {
        Route::get('dashboard', 'DashboardController@dashboard')->name('dashboard');
        Route::get('dashboard/weather', 'DashboardController@weather')->name('dashboard.weather');
        Route::get('dashboard/sunmoon-time', 'DashboardController@sunmoonTime')->name('dashboard.sunmoon-time');
    });
});

# Member Plans & Subscriptions
Route::group($defaultRouteGroup, function () {
    Route::group(['middleware' => ['user', 'un-subscribed']], function () {
        Route::get('plans', 'PlansController@index')->name('subscription.plans');
    });
});
Route::get('braintree/token', 'BraintreeController@token')->name('braintree.token');
Route::group(['middleware' => ['un-subscribed']], function () {
    Route::post('subscription', 'SubscriptionsController@store')->name('subscription');
});
Route::post('braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');
# End Member Plans & Subscriptions

# Complete missed profile information
Route::group(['prefix' => 'account', 'middleware' => [\App\Http\Middleware\BackendUserRedirect::class, 'user']], function () {
    Route::get('complete/profile', ['as' => 'complete.profile.index', 'uses' => 'MissedProfileInformationController@index']);
    Route::put('complete/profile/store', ['as' => 'complete.profile.store', 'uses' => 'MissedProfileInformationController@store']);
});
# End Complete missed profile information

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Member account routes ///////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::pattern('related_member_id', '-|[0-9]+');

$accountRouteGroup = [
    'prefix' => 'account',
    'middleware' => [
        \App\Http\Middleware\BackendUserRedirect::class,
        'related-profile',
        'user',
        'complete-profile'
    ]
];

Route::group($defaultRouteGroup, function () use ($accountRouteGroup) {
    Route::group($accountRouteGroup, function () {
        # Dashboard
        Route::group(['as' => 'account.'], function () {
            Route::get('{related_member_id}/dashboard', 'AccountController@dashboard')->name('dashboard');
        });

        # Overview
        Route::group(['as' => 'account.'], function () {
            Route::get('overview', 'AccountController@overview')->name('overview');
            Route::get('change-password', 'AccountController@changePassword')->name('change-password');
            Route::put('change-password', 'AccountController@changePasswordUpdate')->name('change-password.update');

            # Profile link QR
            Route::get('qr.png', 'AccountController@QRCode')->name('qr');
            Route::get('download/qr', 'AccountController@QRCodeDownload')->name('qr.download');

            //?
            Route::group(['middleware' => 'member'], function () {
                Route::get('subscriptions/resume/{id}', 'SubscriptionsController@resumeSubscription')->name('subscription-resume');
                Route::get('subscriptions/cancel/{id}', 'SubscriptionsController@cancelSubscription')->name('subscription-cancel');
            });
        });

        // Account Notifications/Messages
        Route::group(['middleware' => 'user', 'as' => 'account.'], function () {
            # Notifications
            Route::group(['prefix' => 'notifications', 'middleware' => 'user', 'as' => 'notifications.'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'NotificationsController@index']);
            });

            # Messages
            Route::group(['prefix' => 'messages', 'middleware' => 'user', 'as' => 'messages.'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'MessagesController@index']);
                Route::put('update/{id}', ['as' => 'update', 'middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'MessagesController@update']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'MessagesController@show']);
                Route::group(['middleware' => 'check-permission:messages.new'], function () {
                    Route::get('create/{member_id}', ['as' => 'create', 'uses' => 'MessagesController@create']);
                    Route::put('store/{member_id}', ['as' => 'store', 'middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'MessagesController@store']);
                    Route::get('search/member', ['as' => 'search-member', 'uses' => 'MessagesController@searchMember']);
                });
            });
        });

        // Related Notifications/Messages
        Route::group(['prefix' => '{related_member_id}', 'middleware' => ['user', 'related-required'], 'as' => 'account.related.'], function () {
            # Notifications
            Route::group(['prefix' => 'notifications', 'middleware' => ['user', 'check-permission:related.notifications'], 'as' => 'notifications.'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'RelatedNotificationsController@index']);
            });

            # Messages
            Route::group(['prefix' => 'messages', 'middleware' => ['user', 'check-permission:related.messages'], 'as' => 'messages.'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'RelatedMessagesController@index']);
                Route::put('update/{id}', ['as' => 'update', 'middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'RelatedMessagesController@update']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'RelatedMessagesController@show']);
            });
        });

        # Events
        Route::group(['prefix' => '{related_member_id}/events', 'middleware' => ['check-permission:events.manage', 'related-required'], 'as' => 'account.events.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'EventsController@myEvents']);
            Route::get('form', ['as' => 'form', 'uses' => 'EventsController@form']);
            Route::get('create', ['as' => 'create', 'uses' => 'EventsController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'EventsController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'EventsController@edit']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'EventsController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'EventsController@delete']);
            Route::post('image/upload', ['as' => 'image.upload', 'uses' => 'EventsController@imageUpload']);
            Route::get('data', ['as' => 'data', 'uses' => 'EventsController@data']);
        });
        # End Events

        # Accounts
        Route::group(['prefix' => 'accounts', 'middleware' => ['check-permission:accounts.manage'], 'as' => 'accounts.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'AccountsController@index']);
            Route::get('/{user_id}/profile', ['as' => 'profile', function ($user_id) {
                return redirect()->route('accounts.profile.contact', ['user_id' => $user_id]);
            }]);
            Route::get('/{user_id}/profile/contact', ['as' => 'profile.contact', 'uses' => 'SubProfileController@contact']);
            Route::put('/{user_id}/profile/contact/update', ['as' => 'profile.contact.update', 'uses' => 'SubProfileController@contactUpdate']);
            Route::get('/{user_id}/profile/photo', ['as' => 'profile.photo', 'uses' => 'SubProfileController@photo']);
            Route::put('/{user_id}/profile/photo/update', ['as' => 'profile.photo.update', 'uses' => 'SubProfileController@photoUpdate']);
            Route::get('/{user_id}/profile/newsletter', ['as' => 'profile.newsletter', 'uses' => 'SubProfileController@newsletter']);
            Route::put('/{user_id}/profile/newsletter/update', ['as' => 'profile.newsletter.update', 'uses' => 'SubProfileController@newsletterUpdate']);
        });

        # Assigned Vessel
        /*Route::group(['middleware' => ['check-permission:vessels.manage'], 'as' => 'account.'], function () {
            Route::get('vessels/assigned/dashboard', ['as' => 'boat.dashboard.redirect', function () {
                $link = \App\Models\Vessels\VesselsCrew::where('user_id', Sentinel::getUser()->getUserId())->first();
                if (!$link) {
                    abort(404, 'You haven\'t assigned to vessel');
                }
                return redirect()->route('account.boat.dashboard', ['boat_id' => $link->vessel_id]);
            }]);
        });*/

        # Vessels
        Route::group(['prefix' => '{related_member_id}', 'middleware' => ['check-permission:vessels.manage'], 'as' => 'account.'], function () {
            Route::get('vessels', ['as' => 'vessels', 'uses' => 'VesselsController@my']);
            Route::group(['middleware' => ['check-permission:vessels.colors'], 'as' => 'vessels.color.'], function () {
                Route::put('vessels/color/update', ['as' => 'update', 'uses' => 'VesselsController@colorUpdate']);
            });
            Route::get('vessels/add', ['as' => 'vessels.add', 'uses' => 'VesselsController@create']);

            Route::get('vessels/{boat_id}/profile', ['as' => 'vessels.profile', function ($related_member_id, $boat_id) {
                return redirect()->route('account.vessels.profile.details', ['boat_id' => $boat_id]);
            }]);
            Route::get('vessels/{boat_id}/profile/details', ['as' => 'vessels.profile.details', 'uses' => 'VesselsController@edit']);
            Route::get('vessels/{boat_id}/profile/attachments', ['as' => 'vessels.profile.attachments', 'uses' => 'VesselsPublicAttachmentsController@index']);
            Route::post('vessels/{boat_id}/profile/attachments/upload', ['as' => 'vessels.profile.attachments.upload', 'uses' => 'VesselsPublicAttachmentsController@upload']);
            Route::get('vessels/{boat_id}/profile/attachments/{id}/details', ['as' => 'vessels.profile.attachments.details', 'uses' => 'VesselsPublicAttachmentsController@publicDetails']);
            Route::get('vessels/{boat_id}/profile/attachments/{id}/remove', ['as' => 'vessels.profile.attachments.remove', 'uses' => 'VesselsPublicAttachmentsController@publicRemove']);
            Route::get('vessels/{boat_id}/profile/video', ['as' => 'vessels.profile.video', 'uses' => 'VesselsController@video']);
            Route::post('vessels/{boat_id}/profile/video/store', ['as' => 'vessels.profile.video.store', 'uses' => 'VesselsController@videoStore']);
            Route::get('vessels/{boat_id}/profile/video/delete', ['as' => 'vessels.profile.video.delete', 'uses' => 'VesselsController@videoDelete']);
            Route::get('vessels/{boat_id}/profile/about', ['as' => 'vessels.profile.about', 'uses' => 'VesselsController@about']);
            Route::post('vessels/{boat_id}/profile/about/update', ['as' => 'vessels.profile.about.update', 'uses' => 'VesselsController@aboutUpdate']);

            Route::get('vessels/manufacturers/data', ['as' => 'vessels.manufacturers.data', 'uses' => 'VesselsController@manufacturersData']);
            Route::post('vessels/store', ['as' => 'vessels.store', 'uses' => 'VesselsController@store']);
            Route::post('vessels/{id}/update', ['as' => 'vessels.update', 'uses' => 'VesselsController@update']);
            Route::get('vessels/{id}/remove', ['as' => 'vessels.remove', 'uses' => 'VesselsController@remove']);
            Route::get('vessels/{vessel_id}/image/{id}/delete', ['as' => 'vessels.images.delete', 'uses' => 'VesselsController@deleteImage']);

            Route::post('vessels/switch-vessel', ['as' => 'vessels.switch-vessel', 'uses' => 'VesselsController@setVessel']);

            Route::group(['middleware' => ['check-permission:vessels.set-location']], function () {
                Route::post('vessels/location', ['as' => 'vessels.location', 'uses' => 'VesselsController@setLocation']);
            });
        });

        # Tender
        Route::group(['prefix' => '{related_member_id}',  'middleware' => ['check-permission:vessels.manage'], 'as' => 'account.'], function () {
            Route::get('tenders/manufacturers/data', ['as' => 'tenders.manufacturers.data', 'uses' => 'TendersController@manufacturersData']);

            Route::get('tenders/add', ['as' => 'tenders.add', 'uses' => 'TendersController@create']);
            Route::post('tenders/store', ['as' => 'tenders.store', 'uses' => 'TendersController@store']);
            Route::get('vessels/tender/{boat_id}/profile', ['as' => 'tenders.profile', 'uses' => 'TendersController@edit']);
            Route::post('tenders/{id}/update', ['as' => 'tenders.update', 'uses' => 'TendersController@update']);
            Route::get('tenders/{id}/remove', ['as' => 'tenders.remove', 'uses' => 'TendersController@remove']);
            Route::get('tenders/{vessel_id}/image/{id}/delete', ['as' => 'tenders.images.delete', 'uses' => 'TendersController@deleteImage']);
        });

        # Boat Crew
        Route::group(['prefix' => '{related_member_id}', 'middleware' => ['check-permission:vessels.crew'], 'as' => 'account.boat.'], function () {
            Route::group(['prefix' => 'crew', 'as' => 'crew.'], function () {
                Route::group(['middleware' => ['check-permission:crew.colors']], function () {
                    Route::put('color/update', ['as' => 'color.update', 'uses' => 'BoatCrewController@colorUpdate']);
                });
                Route::get('/', ['as' => 'index', 'uses' => 'BoatCrewController@index']);
                Route::get('create', ['as' => 'create', 'uses' => 'BoatCrewController@create']);
                Route::post('store', ['as' => 'store', 'uses' => 'BoatCrewController@store']);
                Route::get('{member_id}/assign', ['as' => 'assign', 'uses' => 'BoatCrewController@assignMember']);
                Route::get('{member_id}/unassign', ['as' => 'unassign', 'uses' => 'BoatCrewController@unassignMember']);
                Route::get('{member_id}/remove', ['as' => 'remove', 'uses' => 'BoatCrewController@removeMember']);
                Route::get('{member_id}/cv/view', ['as' => 'view.cv', 'uses' => 'BoatCrewController@viewCV']);
            });
        });
        Route::group(['middleware' => ['check-permission:crew.manage'], 'as' => 'account.'], function () {
            Route::group(['prefix' => 'crew', 'as' => 'crew.'], function () {
                Route::get('crew/{user_id}/profile', ['as' => 'profile', function ($user_id) {
                    return redirect()->route('account.crew.profile.contact', ['user_id' => $user_id]);
                }]);
                Route::get('{user_id}/profile/contact', ['as' => 'profile.contact', 'uses' => 'CrewController@contact']);
                Route::put('{user_id}/profile/contact/update', ['as' => 'profile.contact.update', 'uses' => 'CrewController@contactUpdate']);
                Route::get('{user_id}/profile/photo', ['as' => 'profile.photo', 'uses' => 'CrewController@photo']);
                Route::put('{user_id}/profile/photo/update', ['as' => 'profile.photo.update', 'uses' => 'CrewController@photoUpdate']);
                Route::get('{user_id}/profile/newsletter', ['as' => 'profile.newsletter', 'uses' => 'CrewController@newsletter']);
                Route::put('{user_id}/profile/newsletter/update', ['as' => 'profile.newsletter.update', 'uses' => 'CrewController@newsletterUpdate']);
            });
        });

        # Documents
        Route::group(['prefix' => '{related_member_id}', 'middleware' => ['check-permission:vessels.documents'], 'as' => 'account.'], function () {
            Route::group(['as' => 'documents.'], function () {
                Route::get('documents', ['as' => 'index', 'uses' => 'VesselsDocumentsController@index']);
                Route::get('documents/{id}/details', ['as' => 'details', 'uses' => 'VesselsDocumentsController@details']);
                Route::get('documents/{id}/permission', ['as' => 'permission', 'uses' => 'VesselsDocumentsController@permission']);
                Route::post('documents/upload', ['as' => 'upload', 'uses' => 'VesselsDocumentsController@upload']);
                Route::get('documents/{id}/view', ['as' => 'view', 'uses' => 'VesselsDocumentsController@view']);
                Route::get('documents/{id}/download', ['as' => 'download', 'uses' => 'VesselsDocumentsController@download']);
                Route::get('documents/{id}/remove', ['as' => 'remove', 'uses' => 'VesselsDocumentsController@remove']);
            });
            Route::group(['as' => 'templates.'], function () {
                Route::get('templates', ['as' => 'index', 'uses' => 'VesselsTemplateDocumentsController@index']);
                Route::get('templates/{id}/details', ['as' => 'details', 'uses' => 'VesselsTemplateDocumentsController@details']);
                Route::get('templates/{id}/permission', ['as' => 'permission', 'uses' => 'VesselsTemplateDocumentsController@permission']);
                Route::post('templates/upload', ['as' => 'upload', 'uses' => 'VesselsTemplateDocumentsController@upload']);
                Route::get('templates/{id}/print', ['as' => 'print', 'uses' => 'VesselsTemplateDocumentsController@download']);
                Route::get('templates/{id}/download', ['as' => 'download', 'uses' => 'VesselsTemplateDocumentsController@download']);
                Route::get('templates/{id}/remove', ['as' => 'remove', 'uses' => 'VesselsTemplateDocumentsController@remove']);
            });
        });

        # Vessels Transfer
        Route::group(['prefix' => '{related_member_id}', 'middleware' => ['check-permission:vessels.transfer'], 'as' => 'account.boat.transfer.'], function () {
            Route::get('vessels/{boat_id}/transfer/step/{step}', ['as' => 'step', 'uses' => 'VesselsTransferController@step']);
            Route::post('vessels/{boat_id}/transfer/step/{step}', ['as' => 'step.store', 'uses' => 'VesselsTransferController@step']);
            Route::get('vessels/{boat_id}/transfer/{transfer_id}/details', ['as' => 'details', 'uses' => 'VesselsTransferController@details']);
        });
        Route::group(['as' => 'account.boat.transfer.'], function () {
            Route::get('transfer/confirm/{transfer_id}/key/{key}', ['as' => 'origin_confirm', 'uses' => 'VesselsTransferController@transferConfirm']);
            Route::get('transfer/accept/{transfer_id}/key/{key}', ['as' => 'destination_accept', 'uses' => 'VesselsTransferController@transferAccept']);
        });

        # Jobs
        Route::group(['prefix' => '{related_member_id}/jobs', 'middleware' => ['check-permission:jobs.manage', 'related-required'], 'as' => 'account.jobs.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'VesselsJobsController@jobs']);
            Route::get('search', ['as' => 'search', 'uses' => 'VesselsJobsController@search']);

            # Job creation via wizard
            Route::get('wizard/members', ['as' => 'wizard.members', 'uses' => 'JobWizardController@members']);
            Route::get('wizard/period', ['as' => 'wizard.period', 'uses' => 'JobWizardController@period']);
            Route::post('wizard/period/next', ['as' => 'wizard.period.next', 'uses' => 'JobWizardController@periodStore']);
            Route::get('wizard/job', ['as' => 'wizard.job', 'uses' => 'JobWizardController@create']);
            Route::post('wizard/job/store', ['as' => 'wizard.job.store', 'uses' => 'JobWizardController@store']);

            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'VesselsJobsController@edit']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'VesselsJobsController@update']);
            Route::get('delete/{id}', ['as' => 'delete', 'uses' => 'VesselsJobsController@delete']);
            Route::get('image/delete/{id}', ['as' => 'image.delete', 'uses' => 'VesselsJobsController@deleteImage']);

            Route::get('complete/{id}', ['as' => 'complete', 'uses' => 'VesselsJobsController@complete']);
            Route::put('complete/{id}/store', ['as' => 'complete.store', 'uses' => 'VesselsJobsController@setComplete']);

            Route::get('export', ['as' => 'export', 'uses' => 'VesselsJobsController@export']);

            Route::get('{id}/applications', ['as' => 'applications', 'uses' => 'VesselsJobsController@ticketApplications']);
            Route::get('{ticket_id}/applications/{id}/messages', ['as' => 'applicant.messages', 'uses' => 'VesselsJobsController@ticketApplicantMessages']);
            Route::put('{ticket_id}/applications/{id}/messages/send', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'as' => 'applicant.messages.send', 'uses' => 'VesselsJobsController@ticketApplicantMessagesSend']);
            Route::post('{ticket_id}/applications/{id}/attachments/upload', ['as' => 'applicant.attachments.upload', 'uses' => 'VesselsJobsController@uploadAttachment']);
            Route::get('{ticket_id}/applications/{id}/attachments/{file}/download', ['as' => 'applicant.attachments.download', 'uses' => 'VesselsJobsController@downloadAttachment']);
            Route::get('{ticket_id}/applications/{id}/attachments/{file}/remove', ['as' => 'applicant.attachments.remove', 'uses' => 'VesselsJobsController@removeAttachment']);

            Route::post('{ticket_id}/applications/{id}/apply', ['as' => 'applications.apply-user', 'uses' => 'VesselsJobsController@applyUser']);
            Route::get('{ticket_id}/applications/{id}/apply/ask/details', ['as' => 'applications.ask-details', 'uses' => 'VesselsJobsController@askJobDetails']);
            Route::put('{ticket_id}/applications/{id}/apply/ask/details', ['as' => 'applications.store-details', 'uses' => 'VesselsJobsController@storeJobDetails']);
        });
        Route::group(['prefix' => 'jobs', 'middleware' => ['check-permission:jobs.manage'], 'as' => 'account.jobs.'], function () {
            Route::get('related/{memberId}', ['as' => 'related', 'uses' => 'VesselsJobsController@related']);
        });

        # Tickets
        Route::group(['prefix' => '{related_member_id}/tickets', 'middleware' => ['check-permission:tickets.listing', 'related-required'], 'as' => 'account.tickets.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'TicketsController@index']);
            Route::get('{id}/details', ['as' => 'details', 'uses' => 'TicketsController@details']);
            Route::get('{id}/messages', ['as' => 'messages', 'uses' => 'TicketsController@messages']);
            Route::put('{id}/messages/send', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'as' => 'messages.send', 'uses' => 'TicketsController@send']);
            Route::post('{id}/attachments/upload', ['as' => 'attachments.upload', 'uses' => 'TicketsController@upload']);
            Route::get('{id}/attachments/{file}/download', ['as' => 'attachments.download', 'uses' => 'TicketsController@download']);
            Route::get('{id}/attachments/{file}/remove', ['as' => 'attachments.remove', 'uses' => 'TicketsController@remove']);

            Route::get('related/{memberId}', ['as' => 'related', 'uses' => 'TicketsController@related']);
        });

        # Classifieds
        Route::group(['prefix' => 'classifieds', 'middleware' => ['user', 'check-permission:classifieds.manage'], 'as' => 'classifieds.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ClassifiedsController@myClassifieds']);
            Route::get('manufacturers/data', ['as' => 'manufacturers.data', 'uses' => 'ClassifiedsController@manufacturersData']);
            Route::get('create', ['as' => 'create', 'uses' => 'ClassifiedsController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'ClassifiedsController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'ClassifiedsController@edit']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'ClassifiedsController@update']);
            Route::get('image/delete/{id}', ['as' => 'delete-image', 'uses' => 'ClassifiedsController@deleteImage']);
            Route::get('archive/{id}', ['as' => 'archive', 'uses' => 'ClassifiedsController@archive']);
        });

        # Favorites
        Route::group(['prefix' => '{related_member_id}/favorites', 'as' => 'favorites.'], function () {
            Route::group(['middleware' => 'check-permission:members.favorites'], function () {
                Route::get('members/my', 'MembersFavoritesController@my')->name('members.index');
                Route::get('members/add/{id}', 'MembersFavoritesController@store')->name('members.store');
                Route::get('members/delete/{id}', 'MembersFavoritesController@delete')->name('members.delete');
            });
            Route::group(['middleware' => 'check-permission:classifieds.favorites'], function () {
                Route::get('classifieds/my', 'ClassifiedsFavoritesController@my')->name('classifieds.index');
                Route::get('classifieds/add/{id}', 'ClassifiedsFavoritesController@store')->name('classifieds.store');
                Route::get('classifieds/delete/{id}', 'ClassifiedsFavoritesController@delete')->name('classifieds.delete');
            });
            Route::group(['middleware' => 'check-permission:members.favorites'], function () {
                Route::get('vessels/my', 'VesselsFavoritesController@my')->name('vessels.index');
                Route::get('vessels/add/{id}', 'VesselsFavoritesController@store')->name('vessels.store');
                Route::get('vessels/delete/{id}', 'VesselsFavoritesController@delete')->name('vessels.delete');
            });
            Route::group(['middleware' => 'check-permission:jobs.favorites'], function () {
                Route::get('jobs/my', 'JobsFavoritesController@my')->name('jobs.index');
                Route::get('jobs/add/{id}', 'JobsFavoritesController@store')->name('jobs.store');
                Route::get('jobs/delete/{id}', 'JobsFavoritesController@delete')->name('jobs.delete');
            });
            Route::group(['middleware' => 'check-permission:members.favorites'], function () {
                Route::get('business/my', 'BusinessFavoritesController@my')->name('business.index');
                Route::get('business/add/{id}', 'BusinessFavoritesController@store')->name('business.store');
                Route::get('business/delete/{id}', 'BusinessFavoritesController@delete')->name('business.delete');
            });
            Route::group(['middleware' => 'check-permission:events.favorites'], function () {
                Route::get('events/my', 'EventsFavoritesController@my')->name('events.index');
                Route::get('events/add/{id}', 'EventsFavoritesController@store')->name('events.store');
                Route::get('events/delete/{id}', 'EventsFavoritesController@delete')->name('events.delete');
            });
        });

        # Businesses
        Route::group(['middleware' => ['check-permission:business.manage'], 'as' => 'account.'], function () {
            Route::get('businesses', ['as' => 'businesses', 'uses' => 'BusinessesController@my']);

            Route::get('businesses/{business_id}/dashboard', ['as' => 'businesses.dashboard', 'uses' => 'DashboardController@businessDashboard']);

            Route::get('businesses/add', ['as' => 'businesses.add', 'uses' => 'BusinessesController@create']);
            Route::post('businesses/store', ['as' => 'businesses.store', 'uses' => 'BusinessesController@store']);

            Route::get('businesses/business/{business_id}/profile', ['as' => 'businesses.profile', function ($business_id) {
                return redirect()->route('account.businesses.profile.details', ['business_id' => $business_id]);
            }]);
            Route::get('businesses/business/{business_id}/profile/details', ['as' => 'businesses.profile.details', 'uses' => 'BusinessesController@edit']);
            Route::post('businesses/business/{id}/update', ['as' => 'businesses.profile.details.update', 'uses' => 'BusinessesController@update']);
            Route::get('businesses/business/{business_id}/profile/listing', ['as' => 'businesses.profile.listing', 'uses' => 'BusinessesController@listing']);
            Route::post('businesses/business/{id}/listing/update', ['as' => 'businesses.profile.listing.update', 'uses' => 'BusinessesController@listingUpdate']);
            Route::get('businesses/business/{business_id}/profile/photo', ['as' => 'businesses.profile.photo', 'uses' => 'BusinessesController@photo']);
            Route::post('businesses/business/{id}/photo/update', ['as' => 'businesses.profile.photo.update', 'uses' => 'BusinessesController@photoUpdate']);
            Route::get('businesses/business/{business_id}/profile/video', ['as' => 'businesses.profile.video', 'uses' => 'BusinessesController@video']);
            Route::post('businesses/business/{id}/video/store', ['as' => 'businesses.profile.video.store', 'uses' => 'BusinessesController@videoStore']);
            Route::get('businesses/business/{id}/video/delete', ['as' => 'businesses.profile.video.delete', 'uses' => 'BusinessesController@videoDelete']);
            Route::get('businesses/business/{id}/photo/image/{image_id}/delete', ['as' => 'businesses.profile.photo.images.delete', 'uses' => 'BusinessesController@deleteImage']);
            Route::get('businesses/business/{business_id}/profile/about', ['as' => 'businesses.profile.about', 'uses' => 'BusinessesController@about']);
            Route::post('businesses/business/{id}/about/update', ['as' => 'businesses.profile.about.update', 'uses' => 'BusinessesController@aboutUpdate']);
            Route::get('businesses/business/{business_id}/profile/services', ['as' => 'businesses.profile.services', 'uses' => 'BusinessesController@services']);
            Route::post('businesses/business/{id}/services/update', ['as' => 'businesses.profile.services.update', 'uses' => 'BusinessesController@servicesUpdate']);
            Route::get('businesses/business/{business_id}/profile/service-areas', ['as' => 'businesses.profile.service-areas', 'uses' => 'BusinessesController@serviceAreas']);
            Route::post('businesses/business/{id}/service-areas/update', ['as' => 'businesses.profile.service-areas.update', 'uses' => 'BusinessesController@serviceAreasUpdate']);

            Route::get('businesses/{id}/remove', ['as' => 'businesses.remove', 'uses' => 'BusinessesController@remove']);
        });

        # Business Employees
        Route::group(['prefix' => 'businesses/business/{business_id}/employees', 'middleware' => ['check-permission:employees.manage'], 'as' => 'account.businesses.employees.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'BusinessEmployeesController@index']);
            Route::get('assign', ['as' => 'assign', 'uses' => 'BusinessEmployeesController@assign']);
            Route::post('assign', ['as' => 'assign.store', 'uses' => 'BusinessEmployeesController@assignMember']);
            Route::get('{id}/remove', ['as' => 'remove', 'uses' => 'BusinessEmployeesController@remove']);
            Route::get('{user_id}/profile', ['as' => 'profile', function ($business_id, $user_id) {
                return redirect()->route('account.businesses.employees.profile.contact', ['business_id' => $business_id, 'user_id' => $user_id]);
            }]);
            Route::get('{user_id}/profile/contact', ['as' => 'profile.contact', 'uses' => 'EmployeesProfileController@contact']);
            Route::put('{user_id}/profile/contact/update', ['as' => 'profile.contact.update', 'uses' => 'EmployeesProfileController@contactUpdate']);
            Route::get('{user_id}/profile/photo', ['as' => 'profile.photo', 'uses' => 'EmployeesProfileController@photo']);
            Route::put('{user_id}/profile/photo/update', ['as' => 'profile.photo.update', 'uses' => 'EmployeesProfileController@photoUpdate']);
        });

        # Tasks
        Route::group(['prefix' => '{related_member_id}/tasks', 'middleware' => ['user', 'check-permission:tasks.manage'], 'as' => 'account.tasks.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'TasksController@tasks']);
            Route::post('/', ['as' => 'index', 'uses' => 'TasksController@tasks']);
            Route::get('create', ['as' => 'create', 'uses' => 'TasksController@create']);
            Route::post('store', ['as' => 'store', 'uses' => 'TasksController@store']);
            Route::get('edit/{id}', ['as' => 'edit', 'uses' => 'TasksController@edit']);
            Route::post('update/{id}', ['as' => 'update', 'uses' => 'TasksController@update']);
            Route::get('repeat/{id}', ['as' => 'repeat', 'uses' => 'TasksController@repeat']);
            Route::post('reopen/{id}', ['as' => 'reopen', 'uses' => 'TasksController@reopen']);
            Route::post('share', ['as' => 'share', 'uses' => 'TasksController@share']);
            Route::get('status/{id}/{status}', ['as' => 'status', 'uses' => 'TasksController@status']);
            Route::get('snoozed/{id}/{hours}', ['as' => 'snoozed', 'uses' => 'TasksController@snoozed']);
            Route::get('members/data', ['as' => 'members.data', 'uses' => 'TasksController@membersData']);

            Route::group(['prefix' => 'attributes', 'middleware' => [], 'as' => 'attributes.'], function () {
                Route::post('store', ['as' => 'store', 'uses' => 'TasksAttributesController@store']);
                Route::get('{attribute}/remove', ['as' => 'remove', 'uses' => 'TasksAttributesController@remove']);
            });
        });

        # GeoLocation
        Route::group(['prefix' => 'geo', 'middleware' => ['user'], 'as' => 'geo.'], function () {
            Route::get('city/find', ['as' => 'city.find', 'uses' => 'GeoController@findCity']);
        });
    });
});
# End Member Account

# Dashboard
$dashboardRouteGroup = ['prefix' => 'dashboard', 'middleware' => [\App\Http\Middleware\BackendUserRedirect::class, 'user', 'complete-profile'], 'as' => 'dashboard.'];
Route::group($defaultRouteGroup, function () use ($dashboardRouteGroup) {
    Route::group($dashboardRouteGroup, function () {
        # Shop (Store)
        /*Route::group(['prefix' => 'orders', 'middleware' => 'user', 'as' => 'orders.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'OrdersController@myOrders']);
            Route::get('order/{order}', ['as' => 'order', 'uses' => 'OrdersController@showOrder']);
        });*/

        # Services
        /*Route::group(['prefix' => 'services', 'middleware' => 'user', 'as' => 'services.'], function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ServicesController@categories']);
            Route::get('{category_id}', ['as' => 'category', 'uses' => 'ServicesController@category']);
            Route::get('{category_id}/{slug}', ['as' => 'service', 'uses' => 'ServicesController@service']);
        });*/
    });
});
Route::group($dashboardRouteGroup, function () {
    # Sortable
    Route::post('sort', 'DashboardSortableController@sort')->name('sort');

    # Reminder
    Route::group(['prefix' => '{related_member_id}'], function () {
        Route::get('data/reminder', 'DashboardController@reminderData')->name('data.reminder');
        Route::get('data/events-by-date', ['as' => 'data.events-by-date', 'uses' => 'DashboardController@eventsByDate']);
    });

    # Map
    Route::group(['prefix' => '{related_member_id}'], function () {
        Route::get('map/search', 'DashboardController@mapSearch')->name('map.search');
        Route::get('map/remove/point', 'DashboardController@removePoint')->name('map.remove.point');
    });
});
# End Dashboard

# Member Login
Route::group($defaultRouteGroup, function () {
    Route::group(['middleware' => 'authorized-member-dashboard'], function () {
        Route::get('signin', 'FrontEndController@getLogin')->name('signin');
        Route::post('signin', 'FrontEndController@postLogin')->name('signin-authenticate');
    });
});
# End Member Login

# Member Logout
Route::get('logout', 'FrontEndController@logout')->name('logout');
# End Member Logout

# Sign Up!
Route::group($defaultRouteGroup, function () {
    Route::group(['middleware' => 'authorized-member-dashboard'], function () {
        Route::get('signup', 'SignUpController@index')->name('signup');

        // Signin form
        Route::post('authenticate', 'SignUpController@authenticate')->name('authenticate');

        // Vessel/Transfer signup (Yacht Account signup)
        Route::get('signup/yacht-account/owner-vessel-account', 'SignUpYachtOwnerController@signupOwnerVesselAccount')->name('signup.owner-vessel-account');
        Route::post('signup/yacht-account/owner-vessel-account/store', 'SignUpYachtOwnerController@storeOwnerVesselAccount')->name('signup.owner-vessel-account-store');
        Route::get('signup/yacht-account/owner-transfer-account', 'SignUpYachtOwnerController@signupOwnerTransferAccount')->name('signup.owner-transfer-account');
        Route::post('signup/yacht-account/owner-transfer-account/store', 'SignUpYachtOwnerController@storeOwnerTransferAccount')->name('signup.owner-transfer-account-store');
        Route::get('signup/yacht-account/{id}/vessel-info', 'SignUpYachtOwnerController@signupVesselAccount')->name('signup.owner-account.vessel-info');
        Route::post('signup/yacht-account/{id}/vessel-info/store', 'SignUpYachtOwnerController@storeVesselAccount')->name('signup.owner-account.vessel-info-store');
        Route::get('signup/yacht-account/{id}/payment-info', 'SignUpYachtOwnerController@paymentInfo')->name('signup.owner-account.payment-info');
        Route::post('signup/yacht-account/{id}/payment-info/store', 'SignUpYachtOwnerControllerSignUpYachtOwnerController@paymentInfoStore')->name('signup.owner-account.payment-info-store');

        // Marine Contractor signup
        Route::get('signup/marine-contractor-account/owner-account', 'SignUpMarineContractorController@signupOwnerMarineContractorAccount')->name('signup.owner-marine-contractor-account');
        Route::post('signup/marine-contractor-account/owner-account/store', 'SignUpMarineContractorController@storeOwnerMarineContractorAccount')->name('signup.owner-marine-contractor-account-store');
        Route::get('signup/marine-contractor-account/{id}/business-info', 'SignUpMarineContractorController@signupBusinessAccount')->name('signup.owner-marine-contractor-account.business-info');
        Route::post('signup/marine-contractor-account/{id}/business-info/store', 'SignUpMarineContractorController@storeBusinessAccount')->name('signup.owner-marine-contractor-account.business-info-store');
        Route::get('signup/marine-contractor-account/{id}/payment-info', 'SignUpMarineContractorController@paymentInfo')->name('signup.owner-marine-contractor-account.payment-info');
        Route::post('signup/marine-contractor-account/{id}/payment-info/store', 'SignUpMarineContractorController@paymentInfoStore')->name('signup.owner-marine-contractor-account.payment-info-store');

        // Marinas Shipyards signup
        Route::get('signup/marinas-shipyards-account/owner-account', 'SignUpMarinasShipyardsController@signupOwnerMarinasShipyardsAccount')->name('signup.owner-marinas-shipyards-account');
        Route::post('signup/marinas-shipyards-account/owner-account/store', 'SignUpMarinasShipyardsController@storeOwnerMarinasShipyardsAccount')->name('signup.owner-marinas-shipyards-account-store');
        Route::get('signup/marinas-shipyards-account/{id}/business-info', 'SignUpMarinasShipyardsController@signupBusinessAccount')->name('signup.owner-marinas-shipyards-account.business-info');
        Route::post('signup/marinas-shipyards-account/{id}/business-info/store', 'SignUpMarinasShipyardsController@storeBusinessAccount')->name('signup.owner-marinas-shipyards-account.business-info-store');

        // Land Services signup
        Route::get('signup/land-services-account/owner-account', 'SignUpLandServicesController@signupOwnerLandServicesAccount')->name('signup.owner-land-services-account');
        Route::post('signup/land-services-account/owner-account/store', 'SignUpLandServicesController@storeOwnerLandServicesAccount')->name('signup.owner-land-services-account-store');
        Route::get('signup/land-services-account/{id}/business-info', 'SignUpLandServicesController@signupBusinessAccount')->name('signup.owner-land-services-account.business-info');
        Route::post('signup/land-services-account/{id}/business-info/store', 'SignUpLandServicesController@storeBusinessAccount')->name('signup.owner-land-services-account.business-info-store');

        // Guest signup
        Route::post('signup/member', 'SignUpController@signupMember')->name('signup-member');
    });
});

# Activation
Route::group($defaultRouteGroup, function () {
    Route::get('activate-free-success', ['as' => 'activate-free-success', function () {
        resolve('seotools')->setTitle(trans('general.you_re_almost_there'));

        session()->forget('signup-id');

        return view('signup.activate.activate-free');
    }]);
    Route::get('activate-member-success', ['as' => 'activate-member-success', function () {
        resolve('seotools')->setTitle(trans('general.you_re_almost_there'));

        session()->forget('signup-id');

        return view('signup.activate.activate-member');
    }]);
    Route::get('activate/{userId}/{activationCode}', 'SignUpController@activateForm')->name('activate');
});
Route::post('activate/submit', 'SignUpController@activateMember')->name('activate-submit');

# Email Confirmation
Route::group($defaultRouteGroup, function () {
    Route::get('email/confirmation/{userId}/{confirmationCode}', 'ProfileController@emailConfirmation')->name('email-confirmation');
});

# Forgot Password
Route::group($defaultRouteGroup, function () {
    Route::get('forgot-password', 'ForgotPasswordController@getForgotPassword')->name('forgot-password');
    Route::get('forgot-password-success', ['as' => 'forgot-password-success', function () {
        resolve('seotools')->setTitle(trans('general.forgot_password'));

        return view('forgot-password.forgot-password-success');
    }]);
});
Route::post('forgot-password', 'ForgotPasswordController@postForgotPassword')->name('forgot-password-primary-submit');
Route::post('forgot-password/submit', 'SignUpController@forgotPassword')->name('forgot-password-submit');

# Forgot Password Confirmation
Route::get('forgot-password/{userId}/{passwordResetCode}', 'ForgotPasswordController@getForgotPasswordConfirm')->name('forgot-password-confirm');
Route::post('forgot-password/{userId}/{passwordResetCode}/submit', 'ForgotPasswordController@postForgotPasswordConfirm')->name('forgot-password-confirm-submit');

# Profile
Route::group(['middleware' => [\App\Http\Middleware\BackendUserRedirect::class, 'user', 'complete-profile']], function () use ($defaultRouteGroup) {
    Route::group($defaultRouteGroup, function () {
        Route::get('profile', ['as' => 'my-profile', function () {
            return redirect(route('profile.contact'));
        }]);
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('contact', 'ProfileController@contact')->name('contact');
            Route::get('photo', 'ProfileController@photo')->name('photo');
            Route::get('video', ['middleware' => ['check-permission:profile.video'], 'uses' => 'ProfileController@video'])->name('video');
            Route::get('newsletter', 'ProfileController@newsletter')->name('newsletter');
        });
    });
    Route::group(['prefix' => 'profile', 'middleware' => \App\Http\Middleware\ProfileDuplicateBreakLines::class, 'as' => 'profile.'], function () {
        Route::put('contact', 'ProfileController@contactUpdate')->name('contact.update');
        Route::put('photo', 'ProfileController@photoUpdate')->name('photo.update');
        Route::post('video/store', ['middleware' => ['check-permission:profile.video'], 'uses' => 'ProfileController@videoUpdate'])->name('video.update');
        Route::get('video/delete', ['middleware' => ['check-permission:profile.video'], 'uses' => 'ProfileController@videoDelete'])->name('video.delete');
        Route::put('newsletter', 'ProfileController@newsletterUpdate')->name('newsletter.update');
        Route::get('company-logo/delete', 'ProfileController@deleteCompanyImage')->name('company-logo.delete');
    });
});

# Billing
Route::group($defaultRouteGroup, function () {
    Route::group(['prefix' => 'account', 'middleware' => [\App\Http\Middleware\BackendUserRedirect::class, 'complete-profile']], function () {
        # Payment methods
        Route::group(['middleware' => ['check-permission:billing.payment-methods']], function () {
            Route::get('payment-methods', 'PaymentMethodsController@myPaymentMethods')->name('payment-methods');
            Route::get('payment-methods/add', 'PaymentMethodsController@myPaymentMethodAdd')->name('payment-methods.add');
            Route::post('payment-methods/store', 'PaymentMethodsController@myPaymentMethodStore')->name('payment-methods.store');
            Route::get('payment-methods/{token}/delete', 'PaymentMethodsController@myPaymentMethodDelete')->name('payment-methods.delete');
        });
        # Subscriptions
        Route::group(['middleware' => ['check-permission:billing.subscriptions']], function () {
            Route::get('subscriptions', 'SubscriptionsController@mySubscriptions')->name('subscriptions');
            Route::get('subscriptions/resume/{id}', 'SubscriptionsController@resumeSubscription')->name('subscription-resume');
            Route::get('subscriptions/cancel/{id}', 'SubscriptionsController@cancelSubscription')->name('subscription-cancel');
            Route::get('subscriptions/offers/refresh/{id}', 'SubscriptionsController@refreshExtraOffer')->name('offers-refresh');
        });
        # Invoices
        Route::group(['middleware' => ['check-permission:billing.invoices']], function () {
            Route::get('invoices', 'TransactionsController@myInvoices')->name('invoices');
            Route::get('invoices/{id}/download', 'TransactionsController@downloadInvoice')->name('invoices.download');
        });
    });
});
# End Billing

# Newsletter
Route::group($defaultRouteGroup, function () {
    Route::post('subscribe', 'NewsletterController@subscribe')->name('subscribe');
});

# Contact Form
Route::group($defaultRouteGroup, function () {
    Route::get('contact', 'ContactController@contact')->name('contact');
    Route::group(['middleware' => [\App\Http\Middleware\DuplicateBreakLines::class, \App\Http\Middleware\StripTags::class]], function () {
        Route::post('contact', 'ContactController@store')->name('contact.store');
    });
});

# Members
Route::group($defaultRouteGroup, function () {
    Route::group(['prefix' => 'members', 'middleware' => ['authorized-member'], 'as' => 'members.'], function () {
        Route::get('/', function () {
            return redirect()->route('members.search');
        })->name('index');
        // All registered users can search members, except Land Services
        Route::group(['middleware' => 'deny-role:land_services'], function () {
            Route::get('search', ['as' => 'search', 'uses' => 'MembersController@search']);
            Route::get('reviews', ['as' => 'reviews', 'uses' => 'MembersController@reviews']);

            Route::group(['middleware' => ['public-member-profile']], function () {
                Route::get('businesses/{id}', ['as' => 'business.show', 'uses' => 'MembersController@business']);
                Route::get('vessels/{id}', ['as' => 'vessel.show', 'uses' => 'MembersController@vessel']);
                Route::get('{id}', ['as' => 'show', 'uses' => 'MembersController@show']);
            });
        });
    });
});
# End Members

# Members Contact To
Route::group($defaultRouteGroup, function () {
    Route::group(['middleware' => ['can-contact-to']], function () {
        Route::get('members/contact-to/{member_id}', 'MembersController@contactTo')->name('members.contact-to');
        Route::post('members/send-to/{member_id}', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'MembersController@sendTo'])->name('members.send-to');
    });
});
# End Members Contact To

# Members Send Review
Route::group($defaultRouteGroup, function () {
    Route::group(['middleware' => ['can-send-review']], function () {
        Route::get('members/review/{member_id}', 'MembersController@review')->name('members.review');
        Route::post('members/review/{member_id}', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'MembersController@sendReview'])->name('members.send-review');
    });
});
# End Members Send Review

# Reviews
Route::group($defaultRouteGroup, function () {
    Route::group(['prefix' => 'reviews', 'middleware' => ['deny-role:land_services', 'authorized-member'], 'as' => 'reviews.'], function () {
        Route::get('{id}', ['as' => 'show', 'uses' => 'ReviewsController@show']);
    });
    Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
        Route::get('set-status/{id}/{status}/{key}', ['as' => 'set-status', 'uses' => 'ReviewsController@setStatus']);
    });
});
# End Reviews

# Manufacturers
Route::group($defaultRouteGroup, function () {
    Route::group(['prefix' => 'manufacturers', 'as' => 'manufacturers.'], function () {
        Route::get('set-status/{id}/{status}/{key}', ['as' => 'set-status', 'uses' => 'ManufacturersController@setStatus']);
        Route::get('boat/set-status/{id}/{status}/{key}', ['as' => 'boat.set-status', 'uses' => 'ManufacturersController@boatSetStatus']);
    });
});
# End Manufacturers

# Classifieds
Route::group($defaultRouteGroup, function () {
    Route::get('classifieds', 'ClassifiedsController@search')->name('classifieds');
    Route::get('classifieds/{type}/find', 'ClassifiedsController@find')->name('classifieds.find');
    Route::get('classifieds/{type}/filter', 'ClassifiedsController@filter')->name('classifieds.filter');
    Route::get('classifieds/{category_slug}/{slug}', 'ClassifiedsController@show')->name('classifieds.show');
    Route::get('classifieds/{category_slug}/{slug}/contact', 'ClassifiedsController@contact')->name('classifieds.contact');
    Route::post('classifieds/{category_slug}/{slug}/contact/send', 'ClassifiedsController@send')->name('classifieds.send');
    Route::get('classifieds/{type}/category/{slug}', 'ClassifiedsController@category')->name('classifieds.category');
    Route::get('classifieds/{type}/manufacturer/{id}', 'ClassifiedsController@manufacturer')->name('classifieds.manufacturer');
    Route::get('classifieds/{type}/location/{id}', 'ClassifiedsController@location')->name('classifieds.location');

    Route::get('classifieds/refresh/{id}/{key}', ['as' => 'classifieds.refresh', 'uses' => 'ClassifiedsController@refresh']);
    Route::get('classifieds/deactivate/{id}/{key}', ['as' => 'classifieds.deactivate', 'uses' => 'ClassifiedsController@deactivate']);
});
# End Classifieds

# Jobs (Public Jobs)
Route::group($defaultRouteGroup, function () {
    Route::group(['middleware' => ['authorized-member', 'deny-role:land_services', 'deny-role:owner']], function () {
        Route::get('jobs', function (\Illuminate\Http\Request $request, \App\Http\Controllers\JobsController $controller) {
            return $controller->listing($request);
        })->name('jobs');
        //Route::get('jobs/search', 'JobsController@search')->name('jobs.search');
        /*Route::get('jobs/results', function (\Illuminate\Http\Request $request, \App\Http\Controllers\JobsController $controller) {
            return $controller->listing($request);
        })->name('jobs.results');*/
        Route::get('jobs/{slug}', 'JobsController@show')->name('jobs.show');
        Route::post('jobs/{slug}/charge', 'JobsController@publicJobCharge')->name('jobs.show.charge');
        Route::get('jobs/{slug}/apply', 'JobsController@applyForm')->name('jobs.apply-form');
        Route::put('jobs/{slug}/apply', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'JobsController@apply'])->name('jobs.apply');

        Route::get('jobs/private/{slug}/{related_id}', 'JobsController@privateJob')->name('jobs.show.private');
        Route::post('jobs/private/{slug}/charge/{related_id}', 'JobsController@privateJobCharge')->name('jobs.show.private.charge');
        Route::get('jobs/private/{slug}/apply/{related_id}', 'JobsController@applyPrivate')->name('jobs.apply-private');
    });
    Route::put('jobs/{slug}/apply', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'JobsController@apply'])->name('jobs.apply');
});
# End Jobs

# Jobs Review
Route::group($defaultRouteGroup, function () {
    Route::get('jobs/review/{id}', 'JobsController@review')->name('jobs.review');
    Route::post('jobs/review/{id}', ['middleware' => \App\Http\Middleware\DuplicateBreakLines::class, 'uses' => 'JobsController@sendReview'])->name('jobs.send-review');
});
# End Jobs Review

# Events
Route::group($defaultRouteGroup, function () {
    Route::get('events', 'EventsController@listing')->name('events');
    Route::get('events/{slug}', 'EventsController@show')->name('events.show');
    Route::get('events/data/all', 'EventsController@allData')->name('events.data.all');
});
# End Events

# News
Route::group($defaultRouteGroup, function () {
    Route::get('news', 'NewsController@index')->name('news');
    Route::get('news/{year}/{month}/{slug}', 'NewsController@show')->name('news.show');
});
# End News

# Blog
Route::group($defaultRouteGroup, function () {
    Route::get('blog', 'BlogController@index')->name('blog');
    Route::get('blog/{slug}', 'BlogController@getBlogCategory')->name('blog-category');
    Route::get('blog/tags/{slug}', 'BlogController@getBlogTag')->name('blog-tag');
    Route::get('blog/{category}/{slug}', 'BlogController@getPost')->name('blog-post');
});
//Route::post('news/posts/{blog}/comment', 'BlogController@storeComment')->name('blog-post-comment');
# End Blog

# Our Team
//Route::group($defaultRouteGroup, function () {
//    Route::get('our-team', 'OurTeamController@index')->name('our-team');
//    Route::get('our-team/team-member/{id}', 'OurTeamController@member')->name('team-member');
//});
# End Our Team

# Site Pages
Route::group($defaultRouteGroup, function () {
    Route::get('about-us', function () {
        resolve('seotools')->setTitle(trans('general.about_us'));
        return view('about-us');
    })->name('about-us');
    Route::get('{name?}', 'PagesController@showPage')->name('site-page');
});
# End Site Pages
