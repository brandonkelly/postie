<?php
namespace verbb\postie\providers;

use verbb\postie\Postie;
use verbb\postie\base\Provider;

use Craft;
use craft\helpers\Json;

use craft\commerce\Plugin as Commerce;

use Vyuldashev\XmlToArray\XmlToArray;

class CanadaPost extends Provider
{
    // Properties
    // =========================================================================

    public $name = 'Canada Post';


    // Public Methods
    // =========================================================================

    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('postie/providers/canada-post', ['provider' => $this]);
    }

    public function getServiceList(): array
    {
        return [
            'DOM_EP' => 'Expedited Parcel',
            'DOM_RP' => 'Regular Parcel',
            'DOM_PC' => 'Priority',
            'DOM_XP' => 'Xpresspost',
            'INT_PW_ENV' => 'Priority Worldwide envelope INTL',
            'USA_PW_ENV' => 'Priority Worldwide envelope USA',
            'USA_PW_PAK' => 'Priority Worldwide pak USA',
            'INT_PW_PAK' => 'Priority Worldwide pak INTL',
            'INT_PW_PARCEL' => 'Priority Worldwide parcel INTL',
            'USA_PW_PARCEL' => 'Priority Worldwide parcel USA',
            'INT_XP' => 'Xpresspost International',
            'INT_IP_AIR' => 'International Parcel Air',
            'INT_IP_SURF' => 'International Parcel Surface',
            'INT_TP' => 'Tracked Packet - International',
            'INT_SP_SURF' => 'Small Packet International Surface',
            'INT_SP_AIR' => 'Small Packet International Air',
            'USA_XP' => 'Xpresspost USA',
            'USA_EP' => 'Expedited Parcel USA',
            'USA_TP' => 'Tracked Packet - USA',
            'USA_SP_AIR' => 'Small Packet USA Air',
        ];
    }

    public function fetchShippingRates($order)
    {
        // If we've locally cached the results, return that
        if ($this->_rates) {
            return $this->_rates;
        }

        $storeLocation = Commerce::getInstance()->getAddresses()->getStoreLocationAddress();
        $dimensions = $this->getDimensions($order, 'kg', 'cm');

        //
        // TESTING
        //
        // $storeLocation->zipCode = 'H2B1A0'; 
        // $order->shippingAddress->zipCode = 'K1K4T3';
        // $dimensions['weight'] = 1;
        //
        //
        //

        try {
            $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v3">
    <customer-number>{$this->settings['customerNumber']}</customer-number>
    <parcel-characteristics>
        <weight>{$dimensions['weight']}</weight>
    </parcel-characteristics>
    <origin-postal-code>{$storeLocation->zipCode}</origin-postal-code>
    <destination>
        <domestic>
            <postal-code>{$order->shippingAddress->zipCode}</postal-code>
        </domestic>
    </destination>
</mailing-scenario>
XML;

            $response = $this->_request('POST', 'rs/ship/price', ['body' => $xmlRequest]);

            if (isset($response['price-quotes']['price-quote'])) {
                foreach ($response['price-quotes']['price-quote'] as $service) {
                    $serviceHandle = $this->_getServiceHandle($service['service-code']);

                    $this->_rates[$serviceHandle] = (float)$service['price-details']['due'];
                }
            } else {
                Provider::error($this, 'Response error: `' . json_encode($response) . '`.');
            }
        } catch (\Throwable $e) {
            Provider::error($this, 'API error: `' . $e->getMessage() . ':' . $e->getLine() . '`.');
        }

        return $this->_rates;
    }


    // Private Methods
    // =========================================================================

    private function _getClient()
    {
        if (!$this->_client) {
            $this->_client = Craft::createGuzzleClient([
                'base_uri' => 'https://ct.soa-gw.canadapost.ca',
                'auth' => [
                    $this->settings['username'], $this->settings['password']
                ],
                'headers' => [
                    'Content-Type' => 'application/vnd.cpc.ship.rate-v3+xml',
                    'Accept' => 'application/vnd.cpc.ship.rate-v3+xml',
                ]
            ]);
        }

        return $this->_client;
    }

    private function _request(string $method, string $uri, array $options = [])
    {
        $response = $this->_getClient()->request($method, $uri, $options);

        $xml = simplexml_load_string((string)$response->getBody());

        return XmlToArray::convert($xml->asXml());
    }

    private function _getServiceHandle($value)
    {
        return str_replace('.', '_', $value);
    }
}
