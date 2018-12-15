<?php

namespace DeadComposers;

use DateTime;

use DeadComposers\Utils\CountryCodes;
use DeadComposers\Utils\LicenseYears;
use DeadComposers\Wikidata\WikidataDeadPeople;

class UpdateHandler {
    function __construct() {
        $this->country_codes = new CountryCodes();
        $this->licence_years = new LicenseYears();
        $this->wikidata = new WikidataDeadPeople();
    }

    private function validate_date($date, $format = 'Y-m-d\TH:i:s\Z') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function get_public_domain_years($nationality) {
        return $this->licence_years->get_year($nationality);
    }

    private function get_public_domain_day($death_day, $years) {
        $date = new DateTime($death_day);

        $date->add(
            date_interval_create_from_date_string("$years years")
        );

        return $date->format('Y-m-d');
    }

    private function get_dead_composers($target_countries, $occupations) {
        try {
            $dead_people = $this->wikidata->get_dead_people(
                $target_countries,
                $occupations
            );

            $result = [];
            $result_source_urls = [];
            $result_invalid = [];

            foreach ($dead_people as $item) {
                $name = $item->itemLabel->value;
                $death_day = $item->death->value;
                $birth_day = $item->date_of_birth->value;
                $source_url = $item->item->value;

                // Death date has to be given
                if (!$this->validate_date($death_day)) {
                    array_push($result_invalid, [
                        'reason' => 'invalid_death_date',
                        'source_url' => $source_url
                    ]);
                    continue;
                }

                // Birth date is optional
                if (!$this->validate_date($birth_day)) {
                    $birth_day = null;
                }

                $index = 0;
                $countries = explode('|', $item->countries->value);
                $nationality = false;

                // Find the first nationality
                while (!$nationality && $index < count($countries)) {
                    $nationality = $this->country_codes->get_country_code(
                        $countries[$index]
                    );

                    $index += 1;
                }

                // Nationality has to be given
                if (!$nationality) {
                    array_push($result_invalid, [
                        'reason' => 'invalid_nationality',
                        'source_url' => $source_url
                    ]);
                    continue;
                }

                $public_domain_years = $this->get_public_domain_years($nationality);

                if (!$public_domain_years) {
                    array_push($result_invalid, [
                        'reason' => 'invalid_public_domain_day',
                        'source_url' => $source_url
                    ]);
                    continue;
                }


                $public_domain_day = $this->get_public_domain_day(
                    $death_day,
                    $public_domain_years
                );

                // Is duplicate?
                if (in_array($source_url, $result_source_urls)) {
                    array_push($result_invalid, [
                        'reason' => 'duplicate',
                        'source_url' => $source_url
                    ]);
                    continue;
                } else {
                    array_push($result_source_urls, $source_url);
                }

                array_push($result, [
                    'name' => $name,
                    'nationality' => $nationality,
                    'death_day' => $death_day,
                    'birth_day' => $birth_day,
                    'public_domain_day' => $public_domain_day,
                    'public_domain_years' => $public_domain_years,
                    'source_url' => $source_url
                ]);
            }

            return [
                'entries' => $result,
                'source_urls' => $result_source_urls,
                'invalid_entries' => $result_invalid,
                'invalid_entries_count' => count($dead_people) - count($result)
            ];
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }

    function update_database($db, $table_name, $target_countries, $occupations) {
        $queue = array_chunk($target_countries, 10);

        $data = [
            'entries' => [],
            'source_urls' => [],
            'invalid_entries' => [],
            'invalid_entries_count' => 0,
        ];

        foreach ($queue as $target_countries_partial) {
            $data_partial = $this->get_dead_composers(
                $target_countries_partial,
                $occupations
            );

            $data = [
                'entries' => array_merge($data['entries'], $data_partial['entries']),
                'source_urls' => array_merge($data['source_urls'], $data_partial['source_urls']),
                'invalid_entries' => array_merge($data['invalid_entries'], $data_partial['invalid_entries']),
                'invalid_entries_count' => $data['invalid_entries_count'] + $data_partial['invalid_entries_count'],
            ];
        }

        // Don't add entries which already exist in database
        $duplicate_entries = $db->select($table_name, 'source_url', [
            'OR' => [
                'source_url' => $data['source_urls'],
            ]
        ]);

        $new_entries = array_filter(
            $data['entries'], function($entry) use ($duplicate_entries) {
                return !in_array($entry['source_url'], $duplicate_entries);
            }
        );

        // Add new entries to database
        if (count($new_entries) > 0) {
            $db->insert($table_name, $new_entries);
        }

        return [
            'duplicate_entries_count' => count($duplicate_entries),
            'invalid_entries_count' => $data['invalid_entries_count'],
            'invalid_entries' => $data['invalid_entries'],
            'new_entries_count' => count($new_entries),
            'new_entries' => $new_entries
        ];
    }
}
