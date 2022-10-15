<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Cloudflare
{
    private HttpClientInterface $httpClient;

    public function __construct(
        string $apiToken,
        string $endpoint = 'https://api.cloudflare.com/client/v4/',
        ?HttpClientInterface $httpClient = null,
    ) {
        $httpClient     ??= HttpClient::create();
        $options          = ['auth_bearer' => $apiToken];
        $this->httpClient = ScopingHttpClient::forBaseUri($httpClient, $endpoint, $options);
    }

    public function settings(string $zoneId, string $key, string|int|float|bool|array|null $value): ResponseInterface
    {
        return $this->json($zoneId, 'PATCH', "settings/{$key}", ['value' => $value]);
    }

    public function json(string $zoneId, string $method, string $path, array $data): ResponseInterface
    {
        return $this->httpClient->request($method, "zones/{$zoneId}/{$path}", ['json' => $data]);
    }

    public function editSettings(string $zoneId, iterable $settings): ResponseInterface
    {
        $items = [];
        foreach ($settings as $id => $value) {
            $items[] = ['id' => $id, 'value' => $value];
        }

        return $this->json($zoneId, 'PATCH', 'settings', ['items' => $items]);
    }
}
