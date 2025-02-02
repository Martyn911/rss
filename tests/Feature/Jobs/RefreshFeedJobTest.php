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

namespace Feature\Jobs;

use App\Jobs\RefreshFeedJob;
use App\Models\Feed;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class RefreshFeedJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_posts_for_the_given_feed(): void
    {
        $feed = Feed::factory()->create([
            'url' => 'https://example.com/feed.xml',
            'last_fetched_at' => now()->subDays(10),
        ]);
        $job = new RefreshFeedJob($feed->id);
        Http::fake([
            'example.com/*' => Http::response(
                <<<END
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<rss version="2.0">
  <channel>
    <item>
      <title>BookStack Release v22.06</title>
      <link>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</link>
      <pubDate>Fri, 24 Jun 2022 11:00:00 +0000</pubDate>

      <guid>https://www.bookstackapp.com/blog/bookstack-release-v22-06/</guid>
      <description>A little description</description>
    </item>
  </channel>
</rss>
END
            )
        ]);

        $this->assertEquals(0, $feed->posts()->count());

        dispatch_sync($job);

        /** @var Post[] $posts */
        $posts = $feed->posts()->get();
        $this->assertCount(1, $posts);
        $this->assertGreaterThan(now()->subSeconds(10), $feed->refresh()->last_fetched_at);

        $this->assertDatabaseHas('posts', [
            'feed_id' => $feed->id,
            'title' => 'BookStack Release v22.06',
            'url' => 'https://www.bookstackapp.com/blog/bookstack-release-v22-06/',
            'description' => 'A little description',
            'published_at' => '2022-06-24 11:00:00',
        ]);
    }

    public function test_job_is_unique_per_feed(): void
    {
        $feedA = Feed::factory()->create(['url' => 'https://example.com/feed.xml']);
        $feedB = Feed::factory()->create(['url' => 'https://example-b.com/feed.xml']);

        Queue::fake();

        dispatch(new RefreshFeedJob($feedA->id));
        dispatch(new RefreshFeedJob($feedA->id));
        dispatch(new RefreshFeedJob($feedA->id));
        dispatch(new RefreshFeedJob($feedB->id));
        dispatch(new RefreshFeedJob($feedB->id));

        Queue::assertPushed(RefreshFeedJob::class, 2);
    }
}
