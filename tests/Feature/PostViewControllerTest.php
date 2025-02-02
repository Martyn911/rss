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

namespace Feature;

use App\Models\Feed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\GeneratesTestData;
use Tests\TestCase;

final class PostViewControllerTest extends TestCase
{
    use RefreshDatabase;
    use GeneratesTestData;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        $this->generateStableTestData();
    }

    public function test_home(): void
    {
        // Standard main page
        $resp = $this->get('/');
        $resp->assertInertia(function (Assert $page) {
            $page->component('Posts');
            $page->has('feeds', 3);
            $page->has('posts', 100);
            $page->where('page', 1);
            $page->where('search', '');
            $page->where('feeds.0.name', 'Feed A');
            $page->where('feeds.0.tags.0', '#Tech');
        });

        // Pagination test
        $resp = $this->get('/?page=2');
        $resp->assertInertia(function (Assert $page) {
            $page->component('Posts');
            $page->has('feeds', 3);
            $page->has('posts', 50);
            $page->where('page', 2);
        });

        // Search test
        $resp = $this->get('/?query=Special+title+for');
        $resp->assertInertia(function (Assert $page) {
            $page->component('Posts');
            $page->has('feeds', 3);
            $page->has('posts', 3);
            $page->where('search', 'Special title for');
        });
    }

    public function test_tag(): void
    {
        $resp = $this->get('/t/News');
        $resp->assertInertia(function (Assert $page) {
            $page->component('Posts');
            $page->has('feeds', 1);
            $page->has('posts', 50);
            $page->where('page', 1);
            $page->where('search', '');
            $page->where('tag', 'News');
        });
    }

    public function test_feed(): void
    {
        $resp = $this->get('/f/' . Feed::where('url', 'http://example.com/b.xml')->first()->id);
        $resp->assertInertia(function (Assert $page) {
            $page->component('Posts');
            $page->has('feeds', 1);
            $page->has('posts', 50);
            $page->where('page', 1);
            $page->where('search', '');
            $page->where('tag', '');
            $page->where('feed', true);
        });
    }
}
