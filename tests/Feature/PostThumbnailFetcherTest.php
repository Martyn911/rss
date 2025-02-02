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

use App\Models\Post;
use App\Services\PostThumbnailFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class PostThumbnailFetcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_fetch(): void
    {
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==');
        Http::fake([
            'example.com/*' => Http::response(
                <<<END
<html>
<head>
<meta property="og:image" content="https://exampleb.com/image.png">
</head>
</html>
END
            ),
            'exampleb.com/*' => Http::response($imageData),
        ]);
        $post = Post::factory()->create(['url' => 'http://example.com/cats']);

        $fetcher = new PostThumbnailFetcher();
        $result = $fetcher->fetchAndStoreForPost($post);

        $date = now()->toDateString();
        $this->assertTrue($result);
        $expectedPath = storage_path("app/public/thumbs/{$post->feed_id}/{$date}/{$post->id}.png");
        $this->assertFileExists($expectedPath);

        $content = file_get_contents($expectedPath);
        $this->assertEquals($imageData, $content);
        $this->assertEquals($post->thumbnail, "thumbs/{$post->feed_id}/{$date}/{$post->id}.png");

        unlink($expectedPath);
    }

    public function test_html_encoding_in_opengraph_url_is_decoded(): void
    {
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==');
        Http::fake([
            'example.com/*' => Http::response(
                <<<END
<html>
<head>
<meta property="og:image" content="https://exampleb.com/image.png?a=b&amp;c=d">
</head>
</html>
END
            ),
            'https://exampleb.com/image.png?a=b&c=d' => Http::response($imageData),
            'exampleb.com/*' => Http::response('', 404),
        ]);
        $post = Post::factory()->create(['url' => 'http://example.com/cats']);

        $fetcher = new PostThumbnailFetcher();
        $result = $fetcher->fetchAndStoreForPost($post);
        $this->assertTrue($result);

        $date = now()->toDateString();
        $expectedPath = storage_path("app/public/thumbs/{$post->feed_id}/{$date}/{$post->id}.png");
        unlink($expectedPath);
    }
}
