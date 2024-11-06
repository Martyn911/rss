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

namespace App\Jobs;

use App\Services\FeedProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshFeedJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * How long this unique lock exists for this kind of job.
     */
    public int $uniqueFor = 300;

    private int $feedId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $feedId)
    {
        $this->feedId = $feedId;
        if (in_array(config('app.env'), ['testing', 'local'])) {
            $this->uniqueFor = 3;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(FeedProvider $feedProvider): void
    {
        dump(sprintf('RefreshFeedJob run with params: %s', $this->feedId));
        $feedProvider->fetchAndSave($this->feedId);
    }

    /**
     * Get the unique key for these jobs.
     */
    public function uniqueId()
    {
        return $this->feedId;
    }
}
