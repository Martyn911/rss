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

namespace Feature\Commands;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Tests\GeneratesTestData;
use Tests\TestCase;

final class PrunePostsCommandTest extends TestCase
{
    use RefreshDatabase;
    use GeneratesTestData;

    public function test_command_deletes_posts_older_than_days_given(): void
    {
        Post::factory(11)->create(['published_at' => now()->subDays(2)->toDateTimeString()]);
        Post::factory(13)->create(['published_at' => now()->subHours(12)->toDateTimeString()]);

        $this->assertEquals(24, Post::query()->count());

        $this->artisan('rss:prune-posts --days=1')
            ->expectsConfirmation('This will delete all posts older than 1 day(s). Do you want to continue?', 'yes')
            ->expectsOutput('Deleted 11 posts from the system')
            ->assertExitCode(0);

        $this->assertEquals(13, Post::query()->count());
    }

    public function test_command_deletes_post_thumbnail_if_existing(): void
    {
        $post = Post::factory()->createOne(['published_at' => now()->subDay()->subHour()->toDateTimeString()]);
        $thumb = 'thumbs/' . Str::random() . '.png';
        $post->thumbnail = $thumb;
        $post->save();

        Storage::disk('public')->put($thumb, 'test-img-data');

        $this->assertTrue(Storage::disk('public')->exists($thumb));

        $this->artisan('rss:prune-posts --days=1')
            ->expectsConfirmation('This will delete all posts older than 1 day(s). Do you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertFalse(Storage::disk('public')->exists($thumb));
    }

    public function test_command_defaults_to_config_option_time(): void
    {
        Post::factory()->createOne(['published_at' => now()->subDays(10)->subHour()]);
        Post::factory()->createOne(['published_at' => now()->subDays(9)->subHours(6)]);
        config()->set('app.prune_posts_after_days', 10);

        $this->assertEquals(2, Post::query()->count());

        $this->artisan('rss:prune-posts')
            ->expectsConfirmation('This will delete all posts older than 10 day(s). Do you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertEquals(1, Post::query()->count());
    }

    public function test_command_defaults_to_no_action_if_config_false(): void
    {
        Post::factory()->createOne(['published_at' => now()->subDays(10)->subHour()]);
        config()->set('app.prune_posts_after_days', false);

        $this->assertEquals(1, Post::query()->count());

        $this->artisan('rss:prune-posts')
            ->expectsOutput('No prune retention time set therefore no posts will be pruned.')
            ->assertExitCode(0);

        $this->assertEquals(1, Post::query()->count());
    }

    public function test_command_deletes_all_posts_in_range(): void
    {
        Post::factory(500)->create(['published_at' => now()->subDays(10)->subHour()]);

        $this->assertEquals(500, Post::query()->count());

        $this->artisan('rss:prune-posts --days=3')
            ->expectsConfirmation('This will delete all posts older than 3 day(s). Do you want to continue?', 'yes')
            ->assertExitCode(0);

        $this->assertEquals(0, Post::query()->count());
    }
}
