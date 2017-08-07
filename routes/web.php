<?php

Route::group(['middleware' => 'web', 'prefix' => 'back'], function () {
    Route::group(['namespace' => 'InetStudio\AdminPanel\Controllers'], function () {
        Route::group(['namespace' => 'Auth'], function () {
            Route::get('login', 'LoginController@showLoginForm')->name('back.login.form');
            Route::post('login', 'LoginController@login')->name('back.login');

            Route::group(['middleware' => 'back.auth'], function () {
                Route::post('logout', 'LoginController@logout')->name('back.logout');
            });
        });

        Route::group(['middleware' => ['back.auth']], function () {
            Route::group(['namespace' => 'ACL', 'prefix' => 'acl'], function () {
                Route::post('roles/suggestions', 'RolesController@getSuggestions')->name('back.acl.roles.getSuggestions');
                Route::any('roles/data', 'RolesController@data')->name('back.acl.roles.data');
                Route::resource('roles', 'RolesController', ['except' => [
                    'show',
                ], 'as' => 'back.acl']);

                Route::post('permissions/suggestions', 'PermissionsController@getSuggestions')->name('back.acl.permissions.getSuggestions');
                Route::any('permissions/data', 'PermissionsController@data')->name('back.acl.permissions.data');
                Route::resource('permissions', 'PermissionsController', ['except' => [
                    'show',
                ], 'as' => 'back.acl']);

                Route::post('users/suggestions', 'UsersController@getSuggestions')->name('back.acl.users.getSuggestions');
                Route::any('users/data', 'UsersController@data')->name('back.acl.users.data');
                Route::resource('users', 'UsersController', ['except' => [
                    'show',
                ], 'as' => 'back.acl']);
            });

            Route::get('/', 'PagesController@showIndexPage')->name('back');
        });
    });
});
