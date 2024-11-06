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

namespace App\Http\Controllers;

use App\Services\FeedProvider;
use App\Services\PostProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Inertia\Inertia;

class PostViewController extends Controller
{
    public function __construct(
        protected PostProvider $postProvider,
        protected FeedProvider $feedProvider,
    )
    {
    }

    /**
     * @param Request $request
     * @param FeedProvider $feedProvider
     * @param PostProvider $postProvider
     * @return \Inertia\Response
     */
    public function home(Request $request)
    {
        $feeds = $this->feedProvider->getAll();
        $posts = $this->postProvider->getPostByFeed($feeds->pluck('id')->toArray(), $request->get('query'));

        return $this->render($request, $feeds, $posts);
    }

    /**
     * @param Request $request
     * @param string $tag
     * @return \Inertia\Response
     */
    public function tag(Request $request, string $tag)
    {
        $feeds = $this->feedProvider->getByTag($tag);
        $posts = $this->postProvider->getPostByFeed($feeds->pluck('id')->toArray(), $request->get('query'));

        return $this->render($request, $feeds, $posts, ['tag' => $tag]);
    }

    /**
     * @param Request $request
     * @param string $feedId
     * @return \Inertia\Response
     */
    public function feed(Request $request, int $feedId)
    {
        $feeds = $this->feedProvider->getById($feedId);
        $posts = $this->postProvider->getPostByFeed($feeds->pluck('id')->toArray(), $request->get('query'));

        return $this->render($request, $feeds, $posts, ['feed' => $feeds->first()]);
    }

    /**
     * @param Request $request
     * @param Collection $feeds
     * @param Paginator $posts
     * @param array $additionalData
     * @return \Inertia\Response
     */
    private function render(Request $request, Collection $feeds, Paginator $posts, array $additionalData = [])
    {
        $coreData = [
            'feeds' => $this->feedProvider->prepareDisplayFeeds($feeds),
            'posts' => $this->postProvider->prepareDisplayPosts($posts),
            'page' => max((int)($request->get('page')), 1),
            'search' => $request->get('query', ''),
            'tag' => '',
            'feed' => '',
        ];

        return Inertia::render('Posts', array_merge($coreData, $additionalData));
    }
}
