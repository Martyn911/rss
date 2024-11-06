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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PostThumbnailFetcher
{
    /**
     * @param Post $post
     * @return bool
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function fetchAndStoreForPost(Post $post): bool
    {
        if (!$imageUrl = $this->getThumbLinkFromUrl($post->url)) {
            return false;
        }

        if (!$imageInfo = $this->downloadImageFromUrl($imageUrl)) {
            return false;
        }

        $date = now()->toDateString();
        $path = "thumbs/{$post->feed_id}/{$date}/{$post->id}.{$imageInfo['extension']}";
        $complete = Storage::disk('public')->put($path, $imageInfo['data']);
        if (!$complete) {
            return false;
        }

        $post->thumbnail = $path;
        $post->save();

        return true;
    }

    /**
     * @param string $url
     * @return array|null
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function downloadImageFromUrl(string $url): ?array
    {
        $imageResponse = Http::timeout(60)->withUserAgent(config('app.rss_bot_user_agent'))->get($url);
        if (!$imageResponse->successful()) {
            return null;
        }

        $imageData = $imageResponse->body();
        // > 1MB
        if (strlen($imageData) > 1000000) {
            return null;
        }

        $tempFile = tmpfile();
        fwrite($tempFile, $imageData);
        $mimeSplit = explode('/', mime_content_type($tempFile) ?: '');
        if (count($mimeSplit) < 2 || $mimeSplit[0] !== 'image') {
            return null;
        }

        return ['data' => $imageData, 'extension' => $mimeSplit[1]];
    }

    /**
     * @param string $url
     * @return string
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function getThumbLinkFromUrl(string $url): string
    {
        $pageResponse = Http::timeout(60)->withUserAgent(config('app.rss_bot_user_agent'))->get($url);
        if (!$pageResponse->successful()) {
            return '';
        }

        $postHead = substr($pageResponse->body(), 0, 1000000);
        $metaMatches = [];
        $metaPattern = '/<meta [^<>]*property=["\']og:image["\'].*?>/';
        if (!preg_match($metaPattern, $postHead, $metaMatches)) {
            return '';
        }

        $linkMatches = [];
        $linkPattern = '/content=["\'](.*?)["\']/';
        if (!preg_match($linkPattern, $metaMatches[0], $linkMatches)) {
            return '';
        }

        $link = html_entity_decode($linkMatches[1]);

        if (!str_starts_with($link, 'https://') && !str_starts_with($link, 'http://')) {
            return '';
        }

        return $link;
    }
}
