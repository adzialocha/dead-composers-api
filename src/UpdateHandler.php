<?php

namespace DeadComposers;

use DateTime;

use DeadComposers\Utils\CountryCodes;
use DeadComposers\Wikidata\WikidataDeadPeople;

class UpdateHandler {
    function __construct() {
        $this->country_codes = new CountryCodes();
        $this->wikidata = new WikidataDeadPeople();
    }

    private function validate_date($date, $format = 'Y-m-d\TH:i:s\Z') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function get_public_domain_day($death_day, $nationality) {
        $date = new DateTime($death_day);

        $date->add(
            date_interval_create_from_date_string('70 years')
        );

        return $date->format('Y-m-d');
    }

    private function get_dead_composers($target_countries, $occupations) {
        try {
            $dead_people = $this->wikidata->get_dead_people(
                $target_countries,
                $occupations
            );

            $response = [];
            $sources= [];

            foreach ($dead_people as $item) {
                $name = $item->itemLabel->value;
                $death_day = $item->death->value;
                $birth_day = $item->date_of_birth->value;
                $source_url = $item->item->value;

                // Death date has to be given
                if (!$this->validate_date($death_day)) {
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
                    continue;
                }

                $public_domain_day = $this->get_public_domain_day(
                    $death_day,
                    $nationality
                );

                // Is duplicate?
                if (in_array($source_url, $sources)) {
                    continue;
                } else {
                    array_push($sources, $source_url);
                }

                array_push($response, [
                    'name' => $name,
                    'nationality' => $nationality,
                    'death_day' => $death_day,
                    'birth_day' => $birth_day,
                    'public_domain_day' => $public_domain_day,
                    'source_url' => $source_url
                ]);
            }

            return $response;
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage();
        }
    }

    function update_database($db, $table_name, $target_countries, $occupations) {
        $data = $this->get_dead_composers(
            $target_countries,
            $occupations
        );

        $data = array_unique($data, SORT_REGULAR);

        $db->query('TRUNCATE TABLE "' . $table_name. '";');

        if (count($data) > 0) {
            $db->insert($table_name, $data);
        }

        return [
            'total_count' => count($data)
        ];
    }
}
