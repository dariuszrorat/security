<?php

Route::get('sentinel', 'security\sentinel\SentinelController@index');
Route::get('sentinel/register', 'security\sentinel\SentinelController@register');
Route::get('sentinel/unregistered', 'security\sentinel\SentinelController@unregistered');
Route::get('sentinel/modified', 'security\sentinel\SentinelController@modified');
Route::get('sentinel/deleted', 'security\sentinel\SentinelController@deleted');
Route::get('sentinel/backup', 'security\sentinel\SentinelController@backup');
Route::post('sentinel/updateone', 'security\sentinel\SentinelController@updateone');