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

use App\Services\PostPruner;
use Illuminate\Console\Command;

class PrunePostsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:prune-posts
                            {--days= : Number of days\' worth of posts to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old posts, and their thumbnails, from the system';

    /**
     * Execute the console command.
     */
    public function handle(PostPruner $pruner): int
    {
        $days = $this->option('days', null);

        if (!is_null($days)) {
            $retention = (int)$days;
        } else {
            $configuredPruneDays = config('app.prune_posts_after_days');

            if (!$configuredPruneDays) {
                $this->line("No prune retention time set therefore no posts will be pruned.");
                return 0;
            }

            $retention = (int)$configuredPruneDays;
        }

        $acceptsRisk = $this->confirm("This will delete all posts older than {$retention} day(s). Do you want to continue?", true);

        if ($acceptsRisk) {
            $deleteCount = $pruner->prune($retention);
            $this->line("Deleted {$deleteCount} posts from the system");
        }

        return 0;
    }
}
