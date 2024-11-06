<?php
/*
 * Copyright (c) 2024 - All Rights Reserved
 *
 * PHP version 7 and 8
 *
 * @author    Serhii Martynenko <martyn922@gmail.com>
 * @copyright 2024 Serhii Martynenko
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostViewController::class, 'home'])->name('home');
Route::get('/t/{tag}', [PostViewController::class, 'tag'])->name('tag-page');
Route::get('/f/{id}', [PostViewController::class, 'feed'])->name('feed-page');

Route::get('/feed/{id}', [FeedController::class, 'get']);
