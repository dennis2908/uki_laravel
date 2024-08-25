<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\TipsterSeasonAPIController;
use App\Http\Controllers\API\TipsterUserAPIController;
use App\Http\Controllers\Api\UserAPIController;
use App\Http\Controllers\Api\TipsterMatchBetAPIController;
use App\Http\Controllers\Api\TipsterUpcomingMatchBetAPIController;
use App\Http\Controllers\Api\TipsterTransactionAPIController;
use App\Http\Controllers\API\TipsterTransactionCancelAPIController;
use App\Http\Controllers\API\FootBallMatchBetAPIController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['cors'])->group(function () {
    Route::group(['prefix' => 'v1'], function () {

        Route::resource('transaction', TipsterTransactionAPIController::class);

        Route::group(['prefix' => 'season'], function () {
            Route::group(['prefix' => 'get'], function () {
                Route::post('', [TipsterSeasonAPIController::class, 'getDataById']);
                Route::group(['prefix' => 'all'], function () {
                    Route::get('', [TipsterSeasonAPIController::class, 'index']);
                    Route::get('upcoming', [TipsterSeasonAPIController::class, 'AllUpComingSession'])->name('season.get.all.upcoming');
                });
            });
            Route::post('active-season', [TipsterSeasonAPIController::class, 'getActiveSeason']);
        });

        Route::group(['prefix' => 'football'], function () {
            Route::group(['prefix' => 'get'], function () {
                Route::group(['prefix' => 'match'], function () {
                    Route::post('time', [FootBallMatchBetAPIController::class, 'getDataByMatchTime'])->name('football.bet.get.match.time');
                    Route::post('', [FootBallMatchBetAPIController::class, 'getDataById'])->name('football.bet.get.match');
                });
                Route::post('by/football/session', [FootBallMatchBetAPIController::class, 'getDataByFS'])->name('football.bet.get.data.by.fs');
            });
        });


        Route::group(['prefix' => 'match'], function () {
            Route::get('upcoming', [App\Http\Controllers\Api\MatchController::class, 'upcoming']);
            Route::post('history', [App\Http\Controllers\Api\MatchController::class, 'history']);
            Route::post('open-bet', [App\Http\Controllers\Api\MatchController::class, 'openBet']);
        });

        Route::group(['prefix' => 'transaction'], function () {
            Route::group(['prefix' => 'cancel'], function () {
                Route::post('', [App\Http\Controllers\Api\TransactionController::class, 'cancel']);
                Route::get('get', [TipsterTransactionCancelAPIController::class, 'index']);
                Route::post('get', [TipsterTransactionCancelAPIController::class, 'getDataById']);
                Route::post('save', [TipsterTransactionCancelAPIController::class, 'store']);
            });
            Route::post('create', [App\Http\Controllers\Api\TransactionController::class, 'store']);
            Route::post('get', [TipsterTransactionAPIController::class, 'getDataById']);
            Route::post('getDataByUserId', [TipsterTransactionAPIController::class, 'getDataByUserId'])->name('transaction.get.data.by.user.id');
            Route::post('getDataByIdNoRedun', [TipsterMatchBetAPIController::class, 'getDataByIdNoRedun'])->name('match.bet.get.data.by.user.id.no.redun');;
            Route::post('getDataByUserId', [TipsterTransactionAPIController::class, 'getDataByUserId'])->name('transaction.get.data.by.user.id');;
        });

        Route::group(['prefix' => 'match'], function () {
            Route::post('upcoming', [App\Http\Controllers\Api\MatchController::class, 'upcoming']);
            Route::post('history', [App\Http\Controllers\Api\MatchController::class, 'history']);
            Route::group(['prefix' => 'ongoing'], function () {
                Route::group(['prefix' => 'bet'], function () {
                    Route::group(['prefix' => 'get'], function () {
                        Route::post('', [TipsterUpcomingMatchBetAPIController::class, 'getDataById']);
                        Route::get('all', [TipsterUpcomingMatchBetAPIController::class, 'index']);
                        Route::post('home/away', [TipsterUpcomingMatchBetAPIController::class, 'getHomeAway']);
                    });
                });
            });
            Route::group(['prefix' => 'bet'], function () {
                Route::group(['prefix' => 'get'], function () {
                    Route::post('', [TipsterMatchBetAPIController::class, 'getDataById']);
                    Route::get('all', [TipsterMatchBetAPIController::class, 'index']);
                    Route::post('home/away', [TipsterMatchBetAPIController::class, 'getHomeAway']);
                });
            });
        });

        Route::group(['prefix' => 'user'], function () {
            Route::group(['prefix' => 'balance'], function () {
                Route::group(['prefix' => 'get'], function () {
                    Route::post('', [TipsterUserAPIController::class, 'getUser']);
                    Route::get('all', [TipsterUserAPIController::class, 'index']);
                });

                Route::post('save', [TipsterUserAPIController::class, 'store']);
            });
            Route::post('get', [UserAPIController::class, 'getUser']);

            Route::post('create', [App\Http\Controllers\Api\UserController::class, 'store']);
            Route::post('most-tipsters', [App\Http\Controllers\Api\UserController::class, 'mostTipsters']);
        });

        Route::group(['prefix' => 'static-content'], function () {
            Route::get('', [App\Http\Controllers\Api\StaticContentController::class, 'index']);
            Route::get('view/{slug}', [App\Http\Controllers\Api\StaticContentController::class, 'view']);
        });
    });
});
