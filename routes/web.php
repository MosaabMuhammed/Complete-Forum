<?php

use App\User;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;

Route::get('/', function() {
    return view('welcome');
});
Auth::routes();
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/threads', 'ThreadsController@index')->name('threads');
Route::get('/threads/create', "ThreadsController@create");
Route::get('/threads/{channel}/{thread}', 'ThreadsController@show');
Route::patch('/threads/{channel}/{thread}', 'ThreadsController@update');
Route::get('/threads/{channel}', 'ThreadsController@index');
Route::delete("threads/{channel}/{thread}", 'ThreadsController@destroy');

Route::post("locked-thread/{thread}", "LockedThreadController@store")->name('locked-thread-store')->middleware('admin');
Route::delete("locked-thread/{thread}", "LockedThreadController@destroy")->name('locked-thread-destroy')->middleware('admin');

Route::post('/threads', "ThreadsController@store")->middleware('redirect-if-not-confirmed');
Route::post("/threads/{channel}/{thread}/replies", 'RepliesController@store');
Route::get("/threads/{channel}/{thread}/replies", 'RepliesController@index');
Route::post("/threads/{channel}/{thread}/subscriptions", 'ThreadSubscribtionController@store');
Route::delete("/threads/{channel}/{thread}/subscriptions", 'ThreadSubscribtionController@destroy');

Route::delete("replies/{reply}", "RepliesController@destroy");
Route::patch("replies/{reply}", "RepliesController@update");
Route::post("/replies/{reply}/favorites", "FavoritesController@store");
Route::delete("/replies/{reply}/favorites", "FavoritesController@destroy");
Route::post("/replies/{reply}/best", "BestReplyController@store")->name('bestReply.store');

Route::get('/profile/{user}', 'ProfilesController@show')->name('profile');
Route::get("/profiles/{user}/notifications", "UserNotificationController@index");
Route::delete("/profiles/{user}/notifications/{notificationId}", 'UserNotificationController@destroy');

Route::get('api/users', 'Api\UsersController@index');
Route::post('api/users/{user}/avatar', 'Api\AvatarsController@store')->middleware('auth')->name('avatar');
Route::get('/register/confirm', 'Auth\RegisterConfirmationController@index')->name('register.confirm');
