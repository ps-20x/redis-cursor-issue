<?php

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    $redis = Redis::connection('external');

    $cursor = null;
    $keys = [];
    $cacheKey = 'nitro:functions:slots:dev:*';
    $maxLoops = 10;
    $currentLoop = 0;

    do {
        ++$currentLoop;
        $response = $redis->scan($cursor, [
            'match' => $cacheKey,
            'count' => 100,
        ]);
        $cursor = $response[0];
        $currentKeys = $response[1];

        $keys = [
            ...$keys,
            ...$currentKeys,
        ];

        dump($cursor, $currentKeys);

        if ($currentLoop >= $maxLoops) {
            dump('Stop looping');
            break;
        }

    } while ($cursor !== '0');

    dd($cursor);
});
