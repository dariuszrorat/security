<?php

Route::get('sentinel/filesystem',              'Security\Sentinel\Http\Controllers\FilesystemController@index');
Route::get('sentinel/filesystem/register',     'Security\Sentinel\Http\Controllers\FilesystemController@register');
Route::get('sentinel/filesystem/unregistered', 'Security\Sentinel\Http\Controllers\FilesystemController@unregistered');
Route::get('sentinel/filesystem/modified',     'Security\Sentinel\Http\Controllers\FilesystemController@modified');
Route::get('sentinel/filesystem/deleted',      'Security\Sentinel\Http\Controllers\FilesystemController@deleted');
Route::get('sentinel/filesystem/backup',       'Security\Sentinel\Http\Controllers\FilesystemController@backup');
Route::post('sentinel/filesystem/updateone',   'Security\Sentinel\Http\Controllers\FilesystemController@updateone');