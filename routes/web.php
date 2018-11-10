<?php

use ReliQArts\Mardin\Helpers\RouteHelper;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

// Model Bindings
Route::model('thread', ReliQArts\Mardin\Contracts\Thread::class);

// Grab the Messages Controller
$messagesController = RouteHelper::getMessagesController();

Route::group(RouteHelper::getRouteGroupBindings(), function () use ($messagesController) {
    Route::get('{type?}', ['as' => 'messages', 'uses' => "${messagesController}@index"])->where(['type' => 'all|unread']);
    Route::post('m/new', ['as' => 'create-message', 'uses' => "${messagesController}@create"]);
    Route::post('mr', ['as' => 'read-message', 'uses' => "${messagesController}@read"]);
    Route::post('mur', ['as' => 'unread-message', 'uses' => "${messagesController}@unread"]);
    Route::post('del', ['as' => 'unread-message', 'uses' => "${messagesController}@delete"]);
    Route::get('c/unread', ['as' => 'unread-messages-count', 'uses' => "${messagesController}@unreadCount"]);
    Route::post('u/{thread}', ['as' => 'update-message', 'uses' => "${messagesController}@update"]);
    Route::post('/', ['as' => 'store-message', 'uses' => "${messagesController}@store"]);
    Route::get('view/{thread}', ['as' => 'show-message', 'uses' => "${messagesController}@show"]);

    Route::get('in/{filter}.json', "${messagesController}@inboxData")->name('in-threads')->where(['type' => 'all|unread|new']);
    Route::get('t/{thread}/messages.json', "${messagesController}@threadMessagesData")->name('in-thread-messages');
});
