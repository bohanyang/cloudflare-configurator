<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Psr\Log\LogLevel;
use Symfony\Config\MonologConfig;

use function sprintf;

return static function (ContainerConfigurator $containerConfigurator, MonologConfig $monolog): void {
    // Deprecations are logged in the dedicated "deprecation" channel when it exists
    $monolog->channels(['deprecation']);

    if ('dev' === $containerConfigurator->env()) {
        $monolog->handler('main')
            ->type('stream')
            ->path(sprintf('%s/%s.log', param('kernel.logs_dir'), param('kernel.environment')))
            ->level(LogLevel::NOTICE)
            ->channels()
                ->elements(['!event', '!deprecation']);

        $monolog->handler('console')
            ->type('console')
            ->processPsr3Messages(false)
            ->channels()
                ->elements(['!event', '!doctrine', '!console']);
    }

    if ('test' === $containerConfigurator->env()) {
        $monolog->handler('main')
            ->type('fingers_crossed')
            ->actionLevel(LogLevel::ERROR)
            ->handler('nested')
            ->excludedHttpCode(404)
            ->excludedHttpCode(405)
            ->channels()
                ->elements(['!event']);

        $monolog->handler('nested')
            ->type('stream')
            ->path(sprintf('%s/%s.log', param('kernel.logs_dir'), param('kernel.environment')))
            ->level(LogLevel::DEBUG);
    }

    if ('prod' === $containerConfigurator->env()) {
        $monolog->handler('main')
            ->type('fingers_crossed')
            ->actionLevel(LogLevel::ERROR)
            ->handler('nested')
            ->excludedHttpCode(400)
            ->excludedHttpCode(401)
            ->excludedHttpCode(403)
            ->excludedHttpCode(404)
            ->excludedHttpCode(405)
            ->excludedHttpCode(422)
            ->excludedHttpCode(429)
            // How many messages should be saved? Prevent memory leaks
            ->bufferSize(50);

        $monolog->handler('nested')
            ->type('stream')
            ->path('php://stderr')
            ->level(LogLevel::DEBUG);

        $monolog->handler('console')
            ->type('console')
            ->processPsr3Messages(false)
            ->channels()
                ->elements(['!event', '!doctrine']);

        $monolog->handler('deprecation')
                ->type('stream')
                ->path('php://stderr')
                ->channels()
                    ->elements(['deprecation']);
    }
};
