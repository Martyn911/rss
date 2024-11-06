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

use App\Services\FeedProvider;
use Illuminate\Console\Command;

class UpdateFeedsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:update-outdated-feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger the update of outdated feeds';

    /**
     * Execute the console command.
     */
    public function handle(FeedProvider $feedProvider): int
    {
        $logs = $feedProvider->outdatedFeedsQueue();
        foreach ($logs as $log) {
            $this->line($log);
        }

        return Command::SUCCESS;
    }
}
