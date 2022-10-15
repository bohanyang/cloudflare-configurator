<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    if ('dev' === $routingConfigurator->env()) {
        $routingConfigurator
            ->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')
            ->prefix('/_wdt');

        $routingConfigurator
            ->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')
            ->prefix('/_profiler');
    }
};
