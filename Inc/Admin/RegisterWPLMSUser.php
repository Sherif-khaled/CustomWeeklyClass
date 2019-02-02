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


namespace Fajr\CustomWeeklyClass\Admin;

class RegisterWPLMSUser{

	public function register(){
		add_action( 'init', array($this, 'get_last_registerd_user'));
        add_action( 'user_register',array($this, 'register_new_user'),10,1);
	}
    /**
     * Get user data after user register.
     * @return data
     */
	function get_last_registerd_user(){

		$args = array(
					    'orderby'      => 'registered', // registered date
					    'order'        => 'DESC',      // last registered goes first
					    'number'       => 1           // limit to the last one, not required
					);
		$users = get_users( $args );

		$last_user_registered = $users[0]; // the first user from the list

		return $last_user_registered->data; // print user_login
	}
	/**
	 * add register user to taxonomy by user role
	 */

	function register_new_user(){

		global $wpdb;

    	$table_name = $wpdb->prefix . "terms";

    	$slug = 'slug_'. $this->get_last_registerd_user()->user_login;

    	$wpdb->insert($table_name,array(

    		'name'  => $this->get_last_registerd_user()->display_name,
    		'slug'  => $slug,

    		));

    	$lastid = $wpdb->insert_id;

    	$table_name = $wpdb->prefix . "term_taxonomy";

    	if (is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');

    	$user_meta = get_userdata($this->get_last_registerd_user()->ID);

		$user_role = $user_meta->roles[0];

    	if($user_role == 'student'){

    		$wpdb->insert($table_name,array(

    		'term_id'     => $lastid,
    		'taxonomy'    => 'wcs-student',
    		'description' => 'student'

    		));
    	}
    	elseif($user_role == 'instructor'){

    		$wpdb->insert($table_name,array(

    		'term_id'     => $lastid,
    		'taxonomy'    => 'wcs-instructor',
    		'description' => 'instructor'

    		));
    	}

    	
    	
	}
}