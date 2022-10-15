<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../src/Controller/', 'attribute');
};
