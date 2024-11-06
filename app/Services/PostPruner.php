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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class PostPruner
{
    /**
     * Prune all posts older than the given number of days.
     * Returns the number of posts deleted.
     */
    public function prune(int $retentionDays): int
    {
        $ids = [];
        Post::query()
            ->where('published_at', '<', now()->subDays($retentionDays))
            ->select(['id', 'thumbnail'])
            ->chunk(250, function (Collection $posts) use (&$ids) {
                array_push($ids, ...$posts->pluck('id')->all());
                $this->deletePostsThumbnails($posts);
            });

        foreach (array_chunk($ids, 250) as $idChunk) {
            Post::query()
                ->whereIn('id', $idChunk)
                ->delete();
        }

        return count($ids);
    }

    /**
     * @param Collection<Post> $posts
     */
    protected function deletePostsThumbnails(Collection $posts)
    {
        $storage = Storage::disk('public');

        foreach ($posts as $post) {
            if ($post->thumbnail && $storage->exists($post->thumbnail)) {
                $storage->delete($post->thumbnail);
            }
        }
    }
}
