<?php
/**
 * Import iCalendar events in your WordPress articles
 * 
 * PHP Version 5.4
 * 
 * @category Plugin
 * @package  ICSShortcode
 * @author   Pierre Rudloff <contact@rudloff.pro>
 * @license  GPL http://www.gnu.org/licenses/gpl.html
 * @link     https://github.com/StrasWeb/html5-simple-video-gallery
 */
/*
Plugin Name: iCalendar Shortcode
Plugin URI: https://github.com/TC-Alsace/wordpress-ics-shortcode
Description: Import iCalendar events in your WordPress articles
Author: Pierre Rudloff
Version: 0.1
Author URI: https://rudloff.pro/
*/

require_once 'ics-parser/class.iCalReader.php';

/**
 * Display events
 * 
 * @param array $atts Shortcode attributes
 * 
 * @return void
 * */
function ICSEvents($atts)
{
    if (isset($atts['locale'])) {
        setlocale(LC_TIME, $atts['locale']);
    }
    if (!isset($atts['nb'])) {
        $atts['nb'] = 5;
    }
    $ical = new ical($atts['url']);
    $events = $ical->sortEventsWithOrder($ical->events());
    $now = time();
    $eventsToDisplay = array();
    foreach ($events as $event) {
        if ($ical->iCalDateToUnixTimestamp(
            $event['DTSTART']
        ) > $now && count($eventsToDisplay) < $atts['nb']) {
            $eventsToDisplay[] = $event;
        }
    }
    $html = '';
    if (empty($eventsToDisplay)) {
        if (isset($atts['noeventsmsg']) ){
            $html .= $atts['noeventsmsg'];
        }
    } else {
        foreach ($eventsToDisplay as $event) {
            $timestamp = $ical->iCalDateToUnixTimestamp($event['DTSTART']);
            $html .= '<ul class="next-date">
                <li><time datetime="'.strftime('%F', $timestamp).'">
                <span>'.strftime('%e', $timestamp).'</span>
                <span>'.strftime('%b', $timestamp).'</span></time></li>
                <li>'.$event['SUMMARY'].'</li>';
            if (!empty($event['LOCATION'])) {
                $html .= '<li>'.$event['LOCATION'].'</li>';
            }
            if (strlen($event['DTSTART']) > 8) {
                $html .= '<li>'.strftime('%Hh%M', $timestamp).'</li>';
            }
            $html .= '</ul>';
        }
    }
    return $html;

}

add_shortcode('ics_events', 'ICSEvents');
?>
