<?php

use Illuminate\Http\Request;

Route::prefix('/users')->group(function () {
    Route::post('/login', 'api\v1\AuthController@login');
    Route::post('/signup', 'api\v1\AuthController@register');
    Route::middleware('auth:api', 'Protect')->post('/logout', 'api\v1\AuthController@logout');
    Route::get('/loggedIn', 'api\v1\AuthController@loggedIn');
    Route::middleware('auth:api')->delete('/DeleteMe', 'api\v1\AuthController@DeleteMe');
    Route::middleware('auth:api')->post('/updateMe', 'api\v1\AuthController@updateMe');
    Route::middleware('auth:api')->post('/updatePassword', 'api\v1\AuthController@updatePassword');
    Route::middleware('auth:api','role:Mod,Admin,user')->get('/me', 'api\v1\user\userController@FindMe');
});
Route::group(['namespace' => 'api\v1','middleware' => 'api', 'prefix' => 'password'], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

Route::prefix('/admin')->group(function () {

    Route::middleware('auth:api','role:Admin')->get('/users', 'api\v1\user\userController@getAll');
    Route::middleware('auth:api','role:Admin')->post('/users', 'api\v1\user\userController@create');
    Route::middleware('auth:api','role:Admin')->get('/users/{id}', 'api\v1\user\userController@getOne');
    Route::middleware('auth:api','role:Admin')->post('/users/update/{id}', 'api\v1\user\userController@update');
    Route::middleware('auth:api','role:Admin')->delete('/users/{id}', 'api\v1\user\userController@delete');

});

Route::group(['namespace' => 'api\v1','middleware' => 'api', 'prefix' => 'admin'],function () {
    Route::middleware('auth:api')->get('/posts', 'AdminPostsController@getAll');
    Route::middleware('auth:api','role:Admin')->post('/posts', 'AdminPostsController@create');
    Route::middleware('auth:api')->get('/post/{id}', 'AdminPostsController@GetOne');
    Route::middleware('auth:api','role:Admin')->post('/post/{id}', 'AdminPostsController@update');
    Route::middleware('auth:api','role:Admin')->delete('/post/{id}', 'AdminPostsController@delete');
});


Route::group(['namespace' => 'api\v1','middleware' => 'api'],function () {
    Route::middleware('auth:api')->get('/categories', 'AdminCategoriesController@getAll');
    Route::middleware('auth:api','role:Admin')->post('/categories', 'AdminCategoriesController@create');
    Route::middleware('auth:api')->get('/categories/{id}', 'AdminCategoriesController@GetOne');
    Route::middleware('auth:api','role:Admin')->post('/categories/{id}', 'AdminCategoriesController@update');
    Route::middleware('auth:api','role:Admin')->delete('/categories/{id}', 'AdminCategoriesController@delete');
});

