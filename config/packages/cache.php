<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $cache = $framework->cache();
    // Unique name of your app: used to compute stable namespaces for cache keys.
    // $cache->prefixSeed();

    // The "app" cache stores to the filesystem by default.
    // The data in this cache should persist between deploys.
    // Other options include:

    // Redis
    // $cache->app('cache.adapter.redis');
    // $cache->defaultRedisProvider('redis://localhost');

    // APCu (not recommended with heavy random-write workloads as memory fragmentation can cause perf issues)
    // $cache->app('cache.adapter.apcu');

    // Namespaced pools use the above "app" backend by default
    // $cache->pool('my.dedicated.cache', null);
};
