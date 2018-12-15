<?php

namespace DeadComposers\Wikidata;

use Exception;

use DeadComposers\Wikidata\WikidataAPI;

class WikidataDeadPeople {
    private $api;

    const SERVICE_LANGUAGE = 'en,fr,de,ru,el,es,fa';

    function __construct() {
        $this->api = new WikidataAPI();
    }

    private function prepare_query_str($query) {
      return implode(' ', array_map(function($val) {
        return "(wd:$val)";
      }, $query));
    }

    private function build_query($target_countries, $occupations) {
        $select = [
          '?item',
          '?itemLabel',
          '(GROUP_CONCAT(DISTINCT ?countryLabel; SEPARATOR="|") AS ?countries)',
          '?death',
          '?date_of_birth'
        ];

        $values = [
          'VALUES (?target_country) { ' . $target_countries . ' } ',
          'VALUES (?occupation) { ' . $occupations . ' } '
        ];

        $items = [
          '?item wdt:P31 wd:Q5.',
          '?item wdt:P569 ?date_of_birth.',
          '?item wdt:P570 ?death.',
          '?item wdt:P27 ?target_country.',
          '?item wdt:P27 ?country.',
          '?item wdt:P106 ?occupation.'
        ];

        $service = [
          'bd:serviceParam wikibase:language "' . self::SERVICE_LANGUAGE . '".',
          '?item rdfs:label ?itemLabel.',
          '?country rdfs:label ?countryLabel.'
        ];

        $query = ''
          . 'SELECT ' . implode(' ', $select) . ' '
          . 'WHERE {'
          . implode(' ', $values)
          . implode(' ', $items)
          . 'SERVICE wikibase:label { ' . implode(' ', $service) . ' } '
          . '} '
          . 'GROUP BY ?item ?itemLabel ?death ?date_of_birth';

        return $query;
    }

    private function filter_results($data) {
      return array_filter($data, function($item) {
        return !preg_match("/Q\d/", $item->itemLabel->value);
      });
    }

    function get_dead_people($target_countries, $occupations) {
        try {
            $query = $this->build_query(
                $this->prepare_query_str($target_countries),
                $this->prepare_query_str($occupations)
            );

            $response = $this->api->fetch($query);

            return $this->filter_results($response);
        } catch (Exception $e) {
            throw new Exception('An API error occurred.');
        }
    }
}
