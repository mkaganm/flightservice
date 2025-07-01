<?php

namespace App\Providers\AirArabia\SOAP\Builder;

class AirPriceRequestBuilder
{
    /**
     * Build an array for OTA_AirPriceRQ SOAP request matching the provided sample XML structure
     *
     * @param array $params
     * @return array
     */
    public static function build(array $params): array
    {
        $attributes = [
            'EchoToken' => $params['EchoToken'] ?? '',
            'PrimaryLangID' => $params['PrimaryLangID'] ?? 'en-us',
            'SequenceNmbr' => $params['SequenceNmbr'] ?? '1',
            'TimeStamp' => $params['TimeStamp'] ?? date('c'),
            'Version' => $params['Version'] ?? '20061.00',
        ];
        if (!empty($params['TransactionIdentifier'])) {
            $attributes['TransactionIdentifier'] = $params['TransactionIdentifier'];
        }

        $request = [
            '_attributes' => $attributes,
            'POS' => [
                'Source' => [
                    '_attributes' => [
                        'TerminalID' => $params['TerminalID'] ?? 'TestUser/Test Runner',
                    ],
                    'RequestorID' => [
                        '_attributes' => [
                            'Type' => $params['RequestorType'] ?? '4',
                            'ID' => $params['RequestorID'] ?? 'Username',
                        ]
                    ],
                    'BookingChannel' => [
                        '_attributes' => [
                            'Type' => $params['BookingChannelType'] ?? '12',
                        ]
                    ]
                ]
            ],
            'AirItinerary' => [
                '_attributes' => [
                    'DirectionInd' => $params['DirectionInd'] ?? 'Return',
                ],
                'OriginDestinationOptions' => [
                    'OriginDestinationOption' => $params['OriginDestinationOption'] ?? []
                ]
            ],
            'TravelerInfoSummary' => [
                'AirTravelerAvail' => [
                    'PassengerTypeQuantity' => $params['PassengerTypeQuantity'] ?? []
                ]
            ]
        ];

        if (!empty($params['BundledServiceSelectionOptions'])) {
            $request['BundledServiceSelectionOptions'] = $params['BundledServiceSelectionOptions'];
        }

        return $request;
    }
}