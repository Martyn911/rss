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

use App\Models\Post;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class PostProvider
{
    /**
     * @param array $feedIds
     * @param string|null $query
     * @return Paginator
     */
    public function getPostByFeed(array $feedIds, string|null $query = null): Paginator
    {
        $subFilter = function (Builder $where) use ($query) {
            $where->where('title', 'like', '%' . $query . '%')
                ->orWhere('description', 'like', '%' . $query . '%');
        };

        return Post::query()
            ->when($query, $subFilter)
            ->with(['feed', 'feed.tags'])
            ->whereIn('feed_id', $feedIds)
            ->orderBy('published_at', 'desc')
            ->simplePaginate(100);
    }

    /**
     * @param Paginator $posts
     * @return array
     */
    public function prepareDisplayPosts(Paginator $posts)
    {
        $posts->getCollection()->transform(function ($item) {
            if (!is_array($item->feed->tags)) {
                $item->feed->tags = $item->feed->tags->map(function ($tag) {
                    return !is_string($tag) ? '#' . $tag->name : $tag;
                })->toArray();
            }
            $item->feed = $item->feed->only(['id', 'name', 'url', 'tags']);

            return $item->only(['title', 'url', 'thumbnail', 'description', 'feed', 'published_at']);
        });

        return $posts->items();
    }
}
