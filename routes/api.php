<?php

use Illuminate\Support\Facades\Route;

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

Route::namespace('Api')->name('api.')->group(function () {

    Route::controller('AppController')->group(function () {
        Route::get('general-setting', 'generalSetting');
        Route::get('get-countries', 'getCountries');
        Route::get('language/{key}', 'getLanguage');
        Route::get('policies', 'policies');
        Route::get('faq', 'faq');
        Route::get('cryptos', 'cryptos');
        Route::get('fiat-gateways', 'fiatGateways');
        Route::get('ad-filter', 'adFilter');
    });

    Route::namespace('Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('login', 'login');
            Route::post('check-token', 'checkToken');
            Route::post('social-login', 'socialLogin');
        });
        Route::post('register', 'RegisterController@register');

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('user-data-submit', 'UserController@userDataSubmit');

        //authorization
        Route::middleware('registration.complete')->controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode');
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-mobile', 'mobileVerification');
            Route::post('verify-g2fa', 'g2faVerification');
        });

        Route::middleware(['check.status'])->group(function () {

            Route::middleware('registration.complete')->group(function () {

                Route::controller('UserController')->group(function () {
                    Route::get('dashboard', 'dashboard');

                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    Route::get('user-info', 'userInfo');
                    //KYC
                    Route::get('kyc-form', 'kycForm');
                    Route::post('kyc-submit', 'kycSubmit');

                    //Report
                    Route::any('deposit/history', 'depositHistory');
                    Route::get('transactions', 'transactions');

                    //Wallets
                    Route::get('wallets', 'wallets');
                    Route::get('single/wallet/{id}', 'singleWallet');

                    //Public Profile
                    Route::get('public-profile/{username}', 'publicProfile');

                    //Referral
                    Route::get('referral/commissions', 'referralCommissions');
                    Route::get('refereed/users', 'myRef');

                    Route::post('add-device-token', 'addDeviceToken');
                    Route::get('push-notifications', 'pushNotifications');
                    Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                    //2FA
                    Route::get('twofactor', 'show2faForm');
                    Route::post('twofactor/enable', 'create2fa');
                    Route::post('twofactor/disable', 'disable2fa');

                    Route::post('delete-account', 'deleteAccount');
                });

                Route::controller('AdvertisementController')->prefix('advertisement')->group(function () {
                    //Ad search
                    Route::get('search', 'search');

                    Route::middleware('kyc')->group(function () {
                        Route::get('index', 'index');
                        Route::get('new', 'new');
                        Route::get('edit/{id}', 'edit');
                        Route::post('store/{id?}', 'store');
                        Route::post('status-update/{id}', 'statusUpdate');
                        Route::get('reviews/{id}', 'reviews')->name('reviews');
                    });
                });

                Route::controller('TradeController')->middleware('kyc')->prefix('trade')->group(function () {
                    Route::get('index', 'index');
                    Route::get('details/{uid}', 'details')->name('user.trade.details');
                    Route::get('new/{id}', 'create');
                    Route::post('store/{id}', 'store');

                    // Trade Operation
                    Route::post('cancel', 'cancel');
                    Route::post('paid', 'paid');
                    Route::post('dispute', 'dispute');
                    Route::post('release', 'release');
                });

                // Trade Chat
                Route::controller('ChatController')->middleware('kyc')->prefix('trade-chat')->group(function () {
                    Route::post('store/{id}', 'store');
                    Route::get('download/{tradeId}/{id}', 'download');
                });

                // Trade Review
                Route::controller('ReviewController')->middleware('kyc')->prefix('trade-review')->group(function () {
                    Route::get('check/{uid}', 'check');
                    Route::post('store/{uid}', 'store');
                });

                // Withdraw
                Route::controller('WithdrawController')->group(function () {
                    Route::middleware('kyc')->group(function () {
                        Route::get('withdraw-request/{id}', 'withdrawMoney');
                        Route::get('past-withdrawals/{id}', 'previousWithdrawals');
                        Route::post('withdraw-request/confirm', 'store');
                    });

                    Route::get('withdraw/history', 'log')->name('withdraw.history');
                });

                // Payment
                Route::controller('PaymentController')->group(function () {
                    Route::get('wallet-generate/{id}', 'walletGenerate');
                });

                Route::controller('TicketController')->prefix('ticket')->group(function () {
                    Route::get('/', 'supportTicket');
                    Route::post('create', 'storeSupportTicket');
                    Route::get('view/{ticket}', 'viewTicket');
                    Route::post('reply/{id}', 'replyTicket');
                    Route::post('close/{id}', 'closeTicket');
                    Route::get('download/{attachment_id}', 'ticketDownload');
                });
            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
    });
});
