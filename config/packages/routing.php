<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $containerConfigurator, FrameworkConfig $framework): void {
    $router = $framework->router()
        ->utf8(true);

    if ('prod' === $containerConfigurator->env()) {
        $router->strictRequirements(null);
    }
};
