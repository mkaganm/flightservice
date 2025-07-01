<?php

namespace App\Providers\AirArabia\REST\Services;

use App\Models\Flight;
use App\Models\SearchRequest;
use App\Providers\AirArabia\REST\Builder\SearchRequestBuilder;
use App\Providers\AirArabia\REST\Parser\SearchResponseParser;
use App\Services\InMemory\InMemoryStorage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class SearchService extends AbstractRESTService
{
    private string $searchUrl;

    public function __construct(?Client $client = null)
    {
        parent::__construct($client);
        $this->searchUrl = $_ENV['AIRARABIA_SEARCH_URL'] ?? '';
    }

    /**
     * @param SearchRequest $searchRequest
     * @return Flight[]
     */
    public function searchFlights(SearchRequest $searchRequest): array
    {
        $requestArr = SearchRequestBuilder::build($searchRequest);

        $token = $this->getToken();

        $request = $this->requestBuilder($token, $requestArr);
        $responseArr = $this->postRequest($request);

        $flights = SearchResponseParser::parse($responseArr, $searchRequest);

        return $flights;
    }

    private function getToken(): ?string
    {
        $token = InMemoryStorage::get(self::TOKEN_MEMORY_KEY);
        if ($token === null) {
            $authService = new AuthService();
            $token = $authService->authenticate();
        }
        return $token;
    }

    private function postRequest(Request $request): array
    {


        try {
            $res = $this->client->sendAsync($request)->wait();

            $body = $res->getBody()->getContents();

            return $this->parseResponse($body);
        } catch (\Exception $e) {
            // Handle error as needed (log, rethrow, etc.)
            return [];
        }
    }

    private function requestBuilder(String $token, $reqArr): Request
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ];
        $body = json_encode($reqArr);
        return new Request(
            'POST',
            $this->searchUrl,
            $headers,
            $body
        );
    }

    private function responseParser()
    {

    }
}