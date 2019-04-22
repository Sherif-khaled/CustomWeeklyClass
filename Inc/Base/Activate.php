<?php

/**
 * Add the wp register user to taxonomies, the class using tow 
 * roles (student , instructor). 
 *
 * @package    Events Schedule WP Plugin
 * @subpackage Custom weekly class
 * @author     Sherif Khaled <sherif.khaleed@gmail.com>
 * @copyright  2019 
 * @since      1.0
 * @license    GPL
 */

namespace Fajr\CustomWeeklyClass\Base;

class Activate{


	public static function activate(){

		global $wpdb;

		$table_name = $wpdb->prefix . 'wk_users_taxonomy';

		// create the ECPT metabox database table
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
		{
			$sql = "CREATE TABLE " . $table_name .
			" (`id` INT(6) NOT NULL AUTO_INCREMENT,
			   `term_id` INT(6) NOT NULL,
			   `user_id` INT(6) NOT NULL,
				UNIQUE KEY id (id)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

	}



}