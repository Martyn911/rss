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

namespace App\Console\Commands;

use App\Models\Feed;
use App\Services\FeedPostFetcher;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestFeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:test-feed {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the provided feed by running a non-queued test import';

    /**
     * Execute the console command.
     */
    public function handle(FeedPostFetcher $postFetcher): int
    {
        $url = $this->argument('url');

        $posts = $postFetcher->fetch((new Feed())->forceFill(['url' => $url]));
        if (count($posts) === 0) {
            $this->error('No posts fetched. Either data could not be fetched or the feed data was not recognised as valid.');
            return Command::FAILURE;
        }

        $feed = Feed::find(551);

        $count = count($posts);
        $this->line("Found {$count} posts:");
        foreach ($posts as $post) {
            $this->line("[{$post['url']}] {$post['title']}");
            $post['published_at'] = Carbon::createFromTimestamp($post['published_at']);
            $db = $feed->posts()->updateOrCreate(
                ['guid' => $post['guid']],
                $post,
            );
        }

        return Command::SUCCESS;
    }
}
