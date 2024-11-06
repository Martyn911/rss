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

use App\Models\Post;
use App\Services\PostThumbnailFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchPostThumbnailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * How long this unique lock exists for this kind of job.
     */
    public int $uniqueFor = 300;

    private int $postId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    /**
     * Execute the job.
     */
    public function handle(PostThumbnailFetcher $thumbnailFetcher): void
    {
        $thumbnailFetcher->fetchAndStoreForPost(Post::where('id', $this->postId)->first());
    }

    /**
     * Get the unique key for these jobs.
     */
    public function uniqueId(): string
    {
        return (string)($this->postId);
    }
}
