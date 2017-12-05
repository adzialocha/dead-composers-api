<?php

namespace DeadComposers;

use DateTime;
use Exception;
use SimpleXMLElement;

use DeadComposers\UpdateHandler;
use DeadComposers\Utils\ICS;

class API {
    const DEFAULT_FORMAT = 'json';
    const DEFAULT_LIMIT = 10;
    const DEFAULT_ORDER = 'DESC';
    const DEFAULT_ORDER_BY = 'public_domain_day';
    const MAX_LIMIT = 10000;

    const CHARSET = 'charset=utf-8';
    const ICS = 'dead-composers.ics';
    const ICS_DESCRIPTION_TEXT = 'Entry date into public domain';

    const TABLE_NAME = 'dead_composers';

    private $valid_columns = [
        'id',
        'name',
        'public_domain_day',
        'birth_day',
        'death_day',
        'nationality',
        'source_url'
    ];

    private $valid_order = [
        'ASC',
        'DESC'
    ];

    private $valid_formats = [
        'ics',
        'json',
        'xml'
    ];

    function __construct($db, $update_key, $target_countries, $occupations) {
        $this->db = $db;
        $this->update_handler = new UpdateHandler();
        $this->update_key = $update_key;

        $this->target_countries = $target_countries;
        $this->occupations = $occupations;
    }

    private function validate_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    private function format_xml($data, &$xml_data) {
        foreach ($data as $key => $value) {
            if( is_numeric($key) ){
                $key = 'composer';
            }

            if (is_array($value) ) {
                $subnode = $xml_data->addChild($key);
                $this->format_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    private function format_ics($data) {
        $calendar_data = array_map(function($composer) {
            $start_date = new DateTime($composer['public_domain_day']);
            $end_date = new DateTime($composer['public_domain_day']);

            return [
              'location' => $composer['nationality'],
              'description' => self::ICS_DESCRIPTION_TEXT,
              'dtstart' => $start_date->format(DateTime::ISO8601),
              'dtend' => $end_date->format(DateTime::ISO8601),
              'summary' => $composer['name'],
              'url' => $composer['source_url']
            ];
        }, $data);

        $ics = new ICS([]);
        $ics->set($calendar_data);

        return $ics->to_string();
    }

    private function set_headers($format) {
        header('Access-Control-Allow-Origin: *');

        if ($format === 'json') {
            header('Content-Type: application/json; ' . self::CHARSET);
        } else if ($format === 'xml') {
            header('Content-Type: application/xml; ' . self::CHARSET);
        } else if ($format === 'ics') {
            header('Content-type: text/calendar; ' . self::CHARSET);
            header('Content-Disposition: attachment; filename=' . self::ICS);
        }
    }

    function handle_request($method, $params) {
        if (
            array_key_exists('format', $params) &&
            in_array($params['format'], $this->valid_formats)
        ) {
            $format = $params['format'];
        } else {
            $format = self::DEFAULT_FORMAT;
        }

        $this->set_headers($format);

        if ($method !== 'GET') {
            return $this->respond_error(405, 'Method Not Allowed', $format);
        }

        if (array_key_exists('update', $params)) {
            return $this->respond_update($params['update'], $format);
        } else {
            return $this->respond_list($params, $format);
        }
    }

    function respond($response_code, $data, $format) {
        http_response_code($response_code);

        if ($format === 'json') {
            return json_encode($data);
        } else if ($format === 'xml') {
            $xml = new SimpleXMLElement('<dead-composers />');
            $this->format_xml($data, $xml);
            return $xml->asXML();
        } else if ($format === 'ics') {
            if ($response_code !== 200) {
                return '';
            }
            return $this->format_ics($data['data']);
        }
    }

    function respond_error($error_code, $error_message, $format) {
        $data = [
            'status' => 'error',
            'error_code' => $error_code,
            'message' => $error_message,
        ];

        return $this->respond($error_code, $data, $format);
    }

    function respond_success($data, $format) {
        return $this->respond(200, $data, $format);
    }

    function respond_update($key, $format) {
        if ($key !== $this->update_key) {
            return $this->respond_error(401, 'Unauthorized', $format);
        }

        try {
            $results = $this->update_handler->update_database(
                $this->db,
                self::TABLE_NAME,
                $this->target_countries,
                $this->occupations
            );

            return $this->respond_success([
                'status' => 'ok',
                'message' => 'Update successful',
                'data' => $results
            ], $format);
        } catch (Exception $e) {
            return $this->respond_error(500, 'Internal Server Error', $format);
        }
    }

    function respond_list($params, $format) {
        if (
            array_key_exists('order_by', $params) &&
            in_array($params['order_by'], $this->valid_columns)
        ) {
            $order_by = $params['order_by'];
        } else {
            $order_by = self::DEFAULT_ORDER_BY;
        }

        if (
            array_key_exists('order', $params) &&
            in_array(strtoupper($params['order']), $this->valid_order)
        ) {
            $order = strtoupper($params['order']);
        } else {
            $order = self::DEFAULT_ORDER;
        }

        if (
            array_key_exists('offset', $params) &&
            is_numeric($params['offset'])
        ) {
            $offset = intval($params['offset'], 10);
        } else {
            $offset = 0;
        }

        if (
            array_key_exists('limit', $params) &&
            is_numeric($params['limit']) &&
            intval($params['limit'], 10) <= self::MAX_LIMIT &&
            intval($params['limit'], 10) > 0
        ) {
            $limit = intval($params['limit'], 10);
        } else {
            $limit = self::DEFAULT_LIMIT;
        }

        $query = [
            'LIMIT' => [$offset, $limit],
            'ORDER' => [$order_by => $order]
        ];

        $sanitized_params = [
            'limit' => $limit,
            'offset' => $offset,
            'order_by' => $order_by,
            'order' => $order
        ];

        $from_date = null;
        $to_date = null;

        if (
            array_key_exists('from', $params) &&
            $this->validate_date($params['from'])
        ) {
            $from_date = new DateTime($params['from']);
            $query['public_domain_day[>=]'] = $from_date->format('Y-m-d');
            $sanitized_params['from'] = $from_date->format('Y-m-d');
        }

        if (
            array_key_exists('to', $params) &&
            $this->validate_date($params['to'])
        ) {
            $to_date = new DateTime($params['to']);
            $query['public_domain_day[<=]'] = $to_date->format('Y-m-d');
            $sanitized_params['to'] = $to_date->format('Y-m-d');
        }

        if (isset($from_date) && isset($to_date) && $from_date > $to_date) {
            return $this->respond_error(400, 'Bad Request', $format);
        }

        $results = $this->db->select(
            self::TABLE_NAME,
            $this->valid_columns,
            $query
        );

        return $this->respond_success([
            'params' => $sanitized_params,
            'status' => 'ok',
            'data' => $results
        ], $format);
    }
}
