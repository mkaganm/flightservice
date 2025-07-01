<?php

namespace App\Providers\AirArabia\SOAP\Services;

abstract class AbstractSOAPService
{
    protected string $wsdl;
    protected array $options;
    protected ?\SoapClient $client = null;

    public function __construct(?string $wsdl = null, array $options = [])
    {
        $this->wsdl = $wsdl ?? ($_ENV['WSDL_URL'] ?? '');
        $this->options = $options;
        if (empty($this->wsdl)) {
            throw new \InvalidArgumentException('SOAP WSDL URL is not set in env or constructor.');
        }
        $this->client = new \SoapClient($this->wsdl, $options);
    }

    /**
     * Call a SOAP method with parameters
     */
    protected function call(string $method, array $params = [])
    {
        if (!$this->client) {
            throw new \RuntimeException('SOAP client not initialized');
        }
        return $this->client->__soapCall($method, [$params]);
    }
}