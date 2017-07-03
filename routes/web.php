<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('imap');
});

Route::post('/start', 'MailboxController@Start');

Route::post('/mailbox', 'MailboxController@Mailbox');

Route::post('/update', 'MailboxController@UpdateDB');

Route::post('/read', 'MailboxController@Read');

Route::post('/stared', 'MailboxController@Stared');

Route::post('/delete', 'MailboxController@Delete');

Route::post('/send', 'SendMessageController@SendEmail');