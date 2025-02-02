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

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'RSS'),

    /*
    |--------------------------------------------------------------------------
    | Application Configuration File
    |--------------------------------------------------------------------------
    |
    | This value specifies the path to an RSS config file on the local
    | filesystem to be loaded as a default option if there's no
    | config set for the current user-level session.
    |
    */

    'feeds_file' => env('RSS_FEEDS_FILE', storage_path('feeds.txt')),

    /*
    |--------------------------------------------------------------------------
    | Application Load Post Thumbnails
    |--------------------------------------------------------------------------
    |
    | This value specifies whether the application should fetch and
    | download RSS post thumbnails since this can take up space
    | and require extra time to process feeds.
    |
    */

    'load_post_thumbnails' => env('APP_LOAD_POST_THUMBNAILS', true),


    /*
    |--------------------------------------------------------------------------
    | Application Feed Update Frequency
    |--------------------------------------------------------------------------
    |
    | This value specifies how often a feed should be updated. This is not a
    | specific guarantee of update on this interval but instead the age in
    | minutes of when a feed would be considered outdated.
    |
    */

    'feed_update_frequency' => env('APP_FEED_UPDATE_FREQUENCY', 60),

    /*
    |--------------------------------------------------------------------------
    | Application Prune Posts After Days
    |--------------------------------------------------------------------------
    |
    | How many days old posts should exist before they're pruned from the system.
    | Setting this to false will disable any auto-pruning otherwise pruning
    | will run on a daily basis.
    |
    */

    'prune_posts_after_days' => env('APP_PRUNE_POSTS_AFTER_DAYS', false),

    /*
     |--------------------------------------------------------------------------
     |User agent of the bot that is used to obtain data
     |--------------------------------------------------------------------------
     */
    'rss_bot_user_agent' => env('RSS_BOT_USER_AGENT', 'RssFetcher/1.0.0'),


    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool)env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => '',
    'asset_url' => '',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
