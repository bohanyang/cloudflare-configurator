<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $containerConfigurator, FrameworkConfig $framework): void {
    // see https://symfony.com/doc/current/reference/configuration/framework.html
    $framework->secret(env('APP_SECRET'));
    // $framework->csrfProtection()
    //     ->enabled(true);
    $framework->httpMethodOverride(false)
        ->trustedProxies(env('TRUSTED_PROXIES'));

    // Enables session support. Note that the session will ONLY be started if you read or write from it.
    // Remove or comment this section to explicitly disable session support.
    $session = $framework->session()
        ->handlerId(null)
        ->cookieSecure('auto')
        ->cookieSamesite('lax')
        ->storageFactoryId('session.storage.factory.native');

    // $framework->esi(true);
    // $framework->fragments(true);
    $framework->phpErrors()
        ->log(true);

    if ('test' === $containerConfigurator->env()) {
        $framework->test(true);
        $session->storageFactoryId('session.storage.factory.mock_file');
    }
};
