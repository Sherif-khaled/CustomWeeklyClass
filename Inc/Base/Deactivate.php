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

class Deactivate{

	public static function deactivate(){
		flush_rewrite_rules();

		//Remove Roles From DB

	}



}