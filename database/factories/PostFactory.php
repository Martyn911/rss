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

namespace Database\Factories;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $url = $this->faker->url() . '?query=' . random_int(0, 1000);
        return [
            'feed_id' => Feed::factory(),
            'published_at' => now()->subHours(random_int(0, 200))->toDateTimeString(),
            'title' => $this->faker->title(),
            'description' => $this->faker->words(50, true),
            'url' => $url,
            'guid' => $url,
            'thumbnail' => ''
        ];
    }
}
