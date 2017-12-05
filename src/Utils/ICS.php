<?php

namespace DeadComposers\Utils;

// https://gist.github.com/jakebellacera/635416 & modified

use DateTime;

class ICS {
    const DT_FORMAT = 'Ymd\THis\Z';
    const D_FORMAT = 'Ymd';
    protected $properties = [];

    private $available_properties = [
        'description',
        'dtend',
        'dtstart',
        'location',
        'summary',
        'url'
    ];

    public function set($arr) {
        foreach ($arr as $entry) {
            $sanitized_entry = [];

            foreach ($entry as $k => $v) {
                  if (in_array($k, $this->available_properties)) {
                      $sanitized_entry[$k] = $this->sanitize_val($v, $k);
                  }
            }

            array_push($this->properties, $sanitized_entry);
        }
    }

    public function to_string() {
        $rows = $this->build_props();
        return implode("\r\n", $rows);
    }

    private function build_props() {
        // Build ICS properties - add header
        $ics_props = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
            'CALSCALE:GREGORIAN',
        );

        // Go through all events
        foreach ($this->properties as $entry) {
            // Build ICS properties - add header
            foreach($entry as $k => $v) {
                $key = strtoupper($k . ($k === 'url' ? ';VALUE=URI' : ''));
                $props[$key] = $v;
            }

            // Set some default values
            $props['DTSTAMP'] = $this->format_timestamp('now');
            $props['UID'] = uniqid();

            // Append properties
            $ics_props[] = 'BEGIN:VEVENT';
            foreach ($props as $k => $v) {
                $ics_props[] = "$k:$v";
            }
            $ics_props[] = 'END:VEVENT';
        }

        // Build ICS properties - add footer
        $ics_props[] = 'END:VCALENDAR';

        return $ics_props;
    }

    private function sanitize_val($val, $key = false) {
        switch($key) {
            case 'dtend':
            case 'dtstart':
                $val = $this->format_date($val);
                break;
            case 'dtstamp':
                $val = $this->format_timestamp($val);
                break;
            default:
                $val = $this->escape_string($val);
        }
        return $val;
    }

    private function format_date($timestamp) {
        $dt = new DateTime($timestamp);
        return $dt->format(self::D_FORMAT);
    }

    private function format_timestamp($timestamp) {
        $dt = new DateTime($timestamp);
        return $dt->format(self::DT_FORMAT);
    }

    private function escape_string($str) {
        return preg_replace('/([\,;])/','\\\$1', $str);
    }
}
