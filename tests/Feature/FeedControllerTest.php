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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\GeneratesTestData;
use Tests\TestCase;

final class FeedControllerTest extends TestCase
{
    use RefreshDatabase;
    use GeneratesTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateStableTestData();
    }

    public function test_get_feed(): void
    {
        $resp = $this->get('/feed/' . Feed::where('url', 'http://example.com/a.xml')->first()->id);
        $resp->assertOk();
        $resp->assertJson([
            'name' => 'Feed A',
            'tags' => ['#Tech', '#News'],
            'url' => 'http://example.com/a.xml'
        ]);
    }

    public function test_non_existing_feed(): void
    {
        $resp = $this->get('/feed/10000');
        $resp->assertNotFound();
    }
}
