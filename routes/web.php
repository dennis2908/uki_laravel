<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    abort(403);
});

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('', [App\Http\Controllers\Admin\DashboardController::class, 'index']);

        Route::group(['prefix' => 'tipster'], function () {
            Route::group(['prefix' => 'user-balances'], function () {
                Route::get('', [App\Http\Controllers\Admin\Tipster\TipsterUserController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Tipster\TipsterUserController::class, 'index']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Tipster\TipsterUserController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Tipster\TipsterUserController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Tipster\TipsterUserController::class, 'delete']);
            });

            Route::group(['prefix' => 'match-bet'], function () {
                Route::get('', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'index']);
                Route::post('dataSeason', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'dataSeason']);
                Route::get('create', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'edit']);
                Route::put('edit/{id}/{idAwayTeam}/{idHomeTeam}', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'update']);
                Route::get('delete/{id}/{idAwayTeam}/{idHomeTeam}', [App\Http\Controllers\Admin\Tipster\MatchBetTeamsController::class, 'delete']);
            });

            Route::group(['prefix' => 'season'], function () {
                Route::get('', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'index']);
                Route::get('create', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Tipster\TipsterSeasonController::class, 'delete']);
            });

            Route::group(['prefix' => 'transaction'], function () {
                Route::get('', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'index']);
                Route::post('dataMatchBet', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'DataMatchBet']);
                Route::get('create', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Tipster\TransactionController::class, 'delete']);
            });

            Route::group(['prefix' => 'transaction-cancel'], function () {
                Route::get('', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'index']);
                Route::post('dataMatchBet', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'DataMatchBet']);
                Route::get('create', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Tipster\TransactionCancelController::class, 'delete']);
            });
        });

        Route::group(['prefix' => 'setting'], function () {
            Route::group(['prefix' => 'users'], function () {
                Route::get('', [App\Http\Controllers\Admin\Setting\UsersController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Setting\UsersController::class, 'index']);
                Route::get('create', [App\Http\Controllers\Admin\Setting\UsersController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Setting\UsersController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Setting\UsersController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Setting\UsersController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Setting\UsersController::class, 'delete']);
            });
            Route::group(['prefix' => 'config'], function () {
                Route::get('', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'index']);
                Route::get('create', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'delete']);
            });
            Route::group(['prefix' => 'static-content'], function () {
                Route::get('', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'index']);
                Route::get('create', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'delete']);
                Route::get('activate/{id}', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'activate']);
                Route::get('deactivate/{id}', [App\Http\Controllers\Admin\Setting\TipsterStaticContentController::class, 'deactivate']);
            });
            Route::group(['prefix' => 'upcoming-football-match'], function () {
                Route::get('', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'index']);
                Route::post('datatable', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'index']);
                Route::post('dataSeason', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'dataSeason']);
                Route::get('create', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'create']);
                Route::post('create', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'store']);
                Route::get('edit/{id}', [App\Http\Controllers\Admin\Setting\TipsterConfigController::class, 'edit']);
                Route::put('edit/{id}', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'update']);
                Route::get('delete/{id}', [App\Http\Controllers\Admin\Setting\TipsterUpcomingFootballController::class, 'delete']);
            });
        });
    });

    Route::group(['middleware' => ['web']], function () {
        Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
    });


    Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::get('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout']);
    Route::get('forget_password', [App\Http\Controllers\ForgetPasswordController::class, 'index']);
    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
});
