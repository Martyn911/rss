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

use App\Console\Commands\ParseFeedsFileCommand;
use App\Console\Commands\PrunePostsCommand;
use App\Console\Commands\UpdateFeedsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ParseFeedsFileCommand::class)->daily();
Schedule::command(UpdateFeedsCommand::class)->everyFiveMinutes();
Schedule::command(PrunePostsCommand::class, ['-n'])->daily();
