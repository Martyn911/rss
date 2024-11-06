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

namespace App\Http\Controllers;

use App\Services\FeedProvider;
use App\Services\PostProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function get(Request $request, int $id, FeedProvider $feedProvider, PostProvider $postProvider): JsonResponse
    {
        $feeds = $feedProvider->prepareDisplayFeeds($feedProvider->getById($id));

        return response()->json($feeds->first(), !$feeds->first() ? 404 : 200);
    }
}
