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
use Fajr\CustomWeeklyClass\Base\BaseController;
class Enqueue extends BaseController
{
	
	function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue'));
	}
	function enqueue(){

		wp_enqueue_script( 'ajax-script', $this->plugin_url . 'assets/js/admin/custom-fields.js', array('jquery'));


	     $localize = array(
	         'ajaxurl' => admin_url( 'admin-ajax.php' )
	     );

	    wp_localize_script( 'ajax-script', 'ajax_object', $localize);

	}
}