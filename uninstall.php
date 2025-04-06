<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://profiles.wordpress.org/digiraldo/
 * @package    DICALAPI_GCalendar
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options from database
delete_option('dicalapi_gcalendar_options');

// Delete any transients we've created
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%dicalapi_gcalendar_cache%'");
