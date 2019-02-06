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

class BaseController{

	public $plugin_path;
	public $plugin_url;
	public $plugin;

	public function __construct(){

		$this->plugin_path = plugin_dir_path( dirname(__FILE__ , 2));
		$this->plugin_url = plugin_dir_url( dirname(__FILE__ , 2) );
		$this->plugin = plugin_basename( dirname(__FILE__ , 3) ) . '/x_book.php';


	}
}
	