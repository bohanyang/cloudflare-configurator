<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\TwigConfig;

return static function (ContainerConfigurator $containerConfigurator, TwigConfig $twig): void {
    $twig->defaultPath(param('kernel.project_dir') . '/templates');

    if ('test' === $containerConfigurator->env()) {
        $twig->strictVariables(true);
    }
};
