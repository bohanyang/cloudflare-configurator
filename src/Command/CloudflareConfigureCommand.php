<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Cloudflare;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

#[AsCommand(
    name: 'cf:configure',
    description: 'Configure a zone on Cloudflare',
)]
class CloudflareConfigureCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('token', InputArgument::REQUIRED, 'API Token')
            ->addArgument('zone-id', InputArgument::REQUIRED, 'Zone ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $zoneId   = $input->getArgument('zone-id');
        $apiToken = $input->getArgument('token');

        $cf        = new Cloudflare($apiToken);
        $responses = [
            $cf->editSettings($zoneId, [
                'ssl' => 'strict',
                'always_use_https' => 'on',
                'min_tls_version' => '1.2',
                'opportunistic_encryption' => 'on',
                'tls_1_3' => 'on',
                'automatic_https_rewrites' => 'off',
                'always_online' => 'off',
                'security_level' => 'medium',
                'challenge_ttl' => '1800',
                'browser_check' => 'on',
                'websockets' => 'on',
                'privacy_pass' => 'off',
                'browser_cache_ttl' => 0,
                'cache_level' => 'simplified',
                'email_obfuscation' => 'off',
                'early_hints' => 'off',
                'brotli' => 'on',
                'minify' => [
                    'css' => 'off',
                    'html' => 'off',
                    'js' => 'off',
                ],
                'mobile_redirect' => [
                    'status' => 'off',
                    'mobile_subdomain' => '',
                    'strip_uri' => false,
                ],
                '0rtt' => 'on',
                'opportunistic_onion' => 'on',
                'http3' => 'on',
                'server_side_exclude' => 'on',
                'hotlink_protection' => 'off',
            ]),
            $cf->json($zoneId, 'PATCH', 'origin_max_http_version', ['value' => '2']),
            $cf->json($zoneId, 'PATCH', 'argo/tiered_caching', ['value' => 'on']),
            $cf->json($zoneId, 'PATCH', 'cache/tiered_cache_smart_topology_enable', ['value' => 'on']),
            $cf->json($zoneId, 'POST', 'flags/products/protocols/changes', [
                'feature' => 'gRPC',
                'value' => true,
            ]),
        ];

        $failed = false;

        foreach ($responses as $response) {
            if (300 > $statusCode = $response->getStatusCode()) {
                $output->writeln([
                    $response->getInfo('url'),
                    $statusCode,
                ]);
                continue;
            }

            $failed = true;
            $output->writeln(
                [
                    $response->getInfo('url'),
                    json_encode(
                        $response->toArray(false),
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    ),
                ],
            );
        }

        return $failed ? Command::FAILURE : Command::SUCCESS;
    }
}
