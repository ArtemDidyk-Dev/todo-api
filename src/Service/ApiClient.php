<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ApiClient implements ApiClientInterface
{
    public function __construct(
        private HttpClientInterface $client,
    ) {

    }

    public function get(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.genratr.com',
            [
                'query' => [
                    'length' => 16,
                    'lowercase' => true,
                    'numbers' => true,
                ],
            ]
        );
        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
