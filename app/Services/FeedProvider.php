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

use App\Jobs\FetchPostThumbnailJob;
use App\Jobs\RefreshFeedJob;
use App\Models\Feed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class FeedProvider
{
    /**
     * @return array
     */
    public function outdatedFeedsQueue(): array
    {
        $info = [];
        foreach ($this->outdatedFeeds() as $feed) {
            $info[] = sprintf('Feed %s add to queue', $feed->url);
            $this->startReloading($feed);
        }

        return $info;
    }

    /**
     * @param $feedId
     * @param FeedPostFetcher $postFetcher
     * @return void
     */
    public function fetchAndSave($feedId, FeedPostFetcher $postFetcher = new FeedPostFetcher()): void
    {
        $feed = Feed::where('id', $feedId)->first();
        $freshPosts = $postFetcher->fetch($feed);
        $loadThumbs = config('app.load_post_thumbnails');
        foreach ($freshPosts as $attributes) {
            $attributes['published_at'] = Carbon::createFromTimestamp($attributes['published_at']);
            $post = $feed->posts()->updateOrCreate(
                ['guid' => $attributes['guid']],
                $attributes,
            );

            if ($loadThumbs && $post->wasRecentlyCreated) {
                dispatch(new FetchPostThumbnailJob($post->id));
            }
        }

        $feed->last_fetched_at = now()->toDateTimeString();
        $feed->save();
    }

    /**
     * @param int $limit
     * @return Collection
     */
    public function getAll(int $limit = 100)
    {
        return Feed::query()->with(['tags'])->limit($limit)->get();
    }

    /**
     * @param string $tag
     * @return Collection
     */
    public function getByTag(string $tag): Collection
    {
        return Feed::query()->whereHas('tags', function (\Illuminate\Database\Eloquent\Builder $query) use ($tag) {
            $query->where('name', $tag);
        })->with(['tags'])->get();
    }

    /**
     * @param int $id
     * @return Collection
     */
    public function getById(int $id): Collection
    {
        return Feed::query()->where('id', $id)->with(['tags'])->get();
    }

    /**
     * @param Collection $feeds
     * @return \Illuminate\Support\Collection
     */
    public function prepareDisplayFeeds($feeds): \Illuminate\Support\Collection
    {
        return $feeds->map(function ($item) {
            $item->tags = $item->tags->map(fn($tag) => '#' . $tag->name)->toArray();
            $item->reloading = true;
            $item->outdated = $this->isOutdated($item);
            return $item->only(['id', 'name', 'url', 'tags', 'reloading', 'outdated']);
        });
    }

    /**
     * @return Collection
     */
    private function outdatedFeeds(): Collection
    {
        return Feed::whereNull('last_fetched_at')->orWhere('last_fetched_at', '<=', now()->subMinutes(config('app.feed_update_frequency')))->get();
    }

    /**
     * @param Feed $feed
     * @return bool
     */
    public function isOutdated(Feed $feed): bool
    {
        return $feed->last_fetched_at <= now()->subMinutes((int)(config('app.feed_update_frequency')));
    }

    /**
     * @param Feed $feed
     * @return void
     */
    private function startReloading(Feed $feed): void
    {
        dispatch(new RefreshFeedJob($feed->id));
    }
}
