<?php

namespace App\Providers\AirArabia\SOAP\Services;

use App\Providers\AirArabia\SOAP\Builder\AirPriceRequestBuilder;
use App\Providers\AirArabia\SOAP\Parser\AirPriceResponseParser;
use SoapClient;
use SoapFault;

class AirPriceReq
{
    private SoapClient $client;
    private string $wsdl;
    private array $options;

    public function __construct(?string $wsdl = null, array $options = [])
    {
        $this->wsdl = $wsdl ?? __DIR__ . '/../Resources/wsdl.xml';
        $this->options = array_merge([
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'connection_timeout' => 30,
        ], $options);
        $this->client = new SoapClient($this->wsdl, $this->options);
    }

    /**
     * Builds and sends a detailed OTA_AirPriceRQ SOAP request
     * @param array $params
     * @return array [success => bool, data|error => mixed]
     */
    public function request(array $params): array
    {
        $request = AirPriceRequestBuilder::build($params);
        try {
            $response = $this->client->__soapCall('OTA_AirPriceRQ', [$request]);
            $parsed = AirPriceResponseParser::parse($response);
            return [
                'success' => true,
                'data' => $parsed,
                'raw' => $response
            ];
        } catch (SoapFault $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
        }
    }


    /**
     * Recursively removes null values from a multi-dimensional array
     */
    private function arrayFilterRecursive(array $input): array
    {
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                $value = $this->arrayFilterRecursive($value);
                if (empty($value)) {
                    unset($input[$key]);
                }
            } elseif ($value === null) {
                unset($input[$key]);
            }
        }
        return $input;
    }
}