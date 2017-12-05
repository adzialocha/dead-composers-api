<?php

namespace DeadComposers\Wikidata;

use Exception;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class WikidataAPI {
    const BASE_URI = 'https://query.wikidata.org/';
    const FORMAT = 'json';
    const TIMEOUT = 60.0;

    private $client;

    function __construct() {
        $this->client = new Client([
            'base_uri' => self::BASE_URI,
            'timeout'  => self::TIMEOUT
        ]);
    }

    function fetch($query) {
        try {
            $response = $this->client->request('GET', 'sparql', [
                'query' => [
                    'query' => $query,
                    'format' => self::FORMAT
                ]
            ]);

            return json_decode($response->getBody())->results->bindings;
        } catch (RequestException $e) {
            throw new Exception('An API error occurred.');
        } catch (ClientException $e) {
            throw new Exception('An API error occurred.');
        }

        return false;
    }
}
