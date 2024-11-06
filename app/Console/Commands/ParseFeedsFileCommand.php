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

use App\Services\FeedsFileParser;
use Illuminate\Console\Command;

class ParseFeedsFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:parse-feeds-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reread the feed list file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new FeedsFileParser())->progress();
    }
}
