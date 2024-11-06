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

namespace App\Services;

use App\Models\Feed;
use App\Models\Post;
use Illuminate\Support\Facades\Http;

class FeedPostFetcher
{
    /**
     * @return Post[]
     */
    public function fetch(Feed $feed, RssParser $rssParser = new RssParser()): array
    {
        $feedResponse = Http::timeout(60)->withUserAgent(config('app.rss_bot_user_agent'))->get($feed->url);
        if (!$feedResponse->successful()) {
            return [];
        }

        $rssData = trim($feedResponse->body());
        return $rssParser->process($rssData);
    }
}
