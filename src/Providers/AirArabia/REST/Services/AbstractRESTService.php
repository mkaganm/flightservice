<?php

namespace App\Providers\AirArabia\REST\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class AbstractRESTService
{
    protected Client $client;

    public const string TOKEN_MEMORY_KEY = 'airarabia_access_token';

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * Send a GET request
     */
    protected function get(string $url, array $headers = [], array $query = []): array
    {
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => $headers,
                'query' => $query
            ]);
            return $this->parseResponse($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            // Handle error as needed (log, rethrow, etc.)
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send a POST request
     */
    protected function post(string $url, array $headers = [], array $data = []): array
    {
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => array_merge(['Content-Type' => 'application/json'], $headers),
                'json' => $data
            ]);
            return $this->parseResponse($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            // Handle error as needed (log, rethrow, etc.)
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Parse the HTTP response (override if needed)
     */
    protected function parseResponse($response): array
    {
        return json_decode($response, true) ?? [];
    }
}