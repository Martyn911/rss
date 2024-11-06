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

namespace Tests;

use App\Config\ConfiguredFeedProvider;
use App\Models\Feed;
use App\Models\Post;
use App\Models\Tag;
use Database\Factories\TagFactory;

trait GeneratesTestData
{
    protected function generateStableTestData()
    {
        $feeds = [
            Feed::factory(['name' => 'Feed A', 'url' => 'http://example.com/a.xml'])
                ->has(Tag::factory(['name' => 'Tech']))
                ->has(Tag::factory(['name' => 'News']))
                ->create(),
            Feed::factory(['name' => 'Feed B', 'url' => 'http://example.com/b.xml'])->create(),
            Feed::factory(['name' => 'Feed C', 'url' => 'http://example.com/c.xml'])->create(),
        ];

        foreach ($feeds as $feed) {
            Post::factory(49)->create(['feed_id' => $feed->id]);
            Post::factory()->create([
                'title' => "Special title for feed {$feed->url}",
                'description' => "Special desc for feed {$feed->url}",
                'feed_id' => $feed->id
            ]);
        }

        return [
            'feeds' => $feeds,
        ];
    }
}
