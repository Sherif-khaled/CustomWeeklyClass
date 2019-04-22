<?php

/*
Plugin Name: Custom weekly class
Plugin URI: http://demo.curlythemes.com/timetable-wordpress-plugin/
Description: add student texonomy and other more features to Events Schedule WP Plugin.
Version: 1.0
Author: Sherif Khaled
Author URI: http://facebook.com/sherif.khaled
Text Domain: WeeklyClass
Domain Path: /lang
*/
// https://pippinsplugins.com/adding-custom-meta-fields-to-taxonomies/comment-page-3/
//https://shibashake.com/wordpress-theme/wordpress-custom-taxonomy-input-panels
defined( 'ABSPATH' ) or die( 'Hey what are you doin here?' );

if( file_exists(dirname(__FILE__).'/vendor/autoload.php')){
    require_once dirname(__FILE__).'/vendor/autoload.php';
}



use Fajr\CustomWeeklyClass\Base\Activate;
use Fajr\CustomWeeklyClass\Base\Deactivate;

/**
 * The code that runs during plugin activate
 */
function activate(){
    Activate::activate();
}

/**
 * The code that runs during plugin deactivate
 */
function deactivate(){
    Deactivate::deactivate();

}
register_activation_hook( __FILE__, 'activate' );
register_deactivation_hook( __FILE__, 'deactivate' );

/**
 * Initialize all the core classesof the plugin
 */
if(class_exists('Fajr\\CustomWeeklyClass\\Init')){
    Fajr\CustomWeeklyClass\Init::register_service();
}
