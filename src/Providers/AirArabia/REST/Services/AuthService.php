<?php

namespace App\Providers\AirArabia\REST\Services;

use App\Services\InMemory\InMemoryStorage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class AuthService extends AbstractRESTService
{
    private const int TOKEN_TTL = 86400; // 1 day
    private string $loginUrl;
    private string $login;
    private string $password;

    public function __construct(?Client $client = null)
    {
        parent::__construct($client);
        $this->loginUrl = $_ENV['AIRARABIA_LOGIN_URL'] ?? '';
        $this->login = $_ENV['AIRARABIA_LOGIN'] ?? '';
        $this->password = $_ENV['AIRARABIA_PASSWORD'] ?? '';
    }

    /**
     * Authenticate and return accessToken or null.
     */
    public function authenticate(): ?string
    {
        if (!$this->loginUrl || !$this->login || !$this->password) {
            return null;
        }
        $request = $this->requestBuilder();
        $responseArr = $this->postRequest($request);

        $token = $this->responseParser($responseArr);
        if ($token) {
            InMemoryStorage::set(self::TOKEN_MEMORY_KEY, $token, self::TOKEN_TTL);
        }
        if (!$token) {
            return null;
        }
        return $token;
    }

    private function requestBuilder(): Request
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $body = json_encode([
            'login' => $this->login,
            'password' => $this->password
        ]);
        return new Request(
            'POST',
            $this->loginUrl,
            $headers,
            $body
        );
    }

    private function responseParser(array $responseArr): ?string
    {
        return $responseArr['tokenPair']['accessToken'] ?? null;
    }

    private function postRequest(Request $request): array
    {
        try {
            $res = $this->client->sendAsync($request)->wait();
            return $this->parseResponse($res->getBody()->getContents());
        } catch (\Exception $e) {
            return [];
        }
    }
}