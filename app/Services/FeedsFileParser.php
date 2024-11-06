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
use App\Models\Tag;

class FeedsFileParser
{
    private $feeds;
    private $feedsFile;

    public function __construct()
    {
        $this->feedsFile = config('app.feeds_file');
        if (!file_exists($this->feedsFile)) {
            throw new \Exception('Feeds file not found: ' . $this->feedsFile);
        }
    }

    /**
     * Parse and save
     * @return void
     */
    public function progress()
    {
        $this->parse() & $this->save();
    }

    /**
     * Parse feeds file
     * @return void
     */
    private function parse(): void
    {
        $contents = file_get_contents($this->feedsFile);
        $lines = explode("\n", $contents);

        foreach ($lines as $line) {
            $line = trim($line);
            $parts = explode(' ', $line);

            if (empty($line) || str_starts_with($line, '#') || count($parts) < 2) {
                continue;
            }

            $url = trim($parts[0]);
            $name = trim($parts[1]);
            $tags = array_filter(array_slice($parts, 2), fn($str) => str_starts_with($str, '#'));
            $tags = array_map(fn($str) => str_replace('#', '', $str), $tags);
            if (isset($this->feeds[$url]) || !str_starts_with($url, 'http')) {
                continue;
            }

            $this->feeds[$url] = (object)[
                'url' => $url,
                'name' => $name,
                'tags' => $tags
            ];
        }
    }

    /**
     * Save feeds to db
     * @return void
     */
    private function save(): void
    {
        foreach ($this->feeds as $feedItem) {
            $feed = Feed::firstOrCreate(['url' => $feedItem->url], ['name' => $feedItem->name]);
            $tagIds = [];
            if ($feedItem->tags) {
                foreach ($feedItem->tags as $tagItem) {
                    $tag = Tag::firstOrCreate(['name' => $tagItem]);
                    $tagIds[] = $tag->id;
                }
            }
            $feed->tags()->sync($tagIds);
        }
    }
}
