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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tag_feed', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Feed::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Tag::class)->index()->constrained()->cascadeOnDelete();
            $table->unique(['feed_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_feed');
    }
};
