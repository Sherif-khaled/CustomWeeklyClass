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
use Fajr\CustomWeeklyClass\Base\Functions;

class RegisterWPLMSUser{

	public function register(){
		//add_action( 'init', array($this, 'get_last_registered_user'));
        add_action( 'user_register',array($this, 'register_new_user'),10,1);
        add_action( 'delete_user',array($this,'delete_wp_user_weekly_data'),10);
	}
    
	/**
	 * add register user to taxonomy by user role
	 */

	function register_new_user(){

		global $wpdb;

        //Insert WP User to wp_terms table.
    	$table_name = $wpdb->prefix . "terms";
    	$slug = 'slug_'. Functions::get_last_registered_user()->user_login;
    	$wpdb->insert($table_name,array(

    		'name'  => Functions::get_last_registered_user()->display_name,
    		'slug'  => $slug,

    		));
        //Get last insert id from terms table.
    	$lastid = $wpdb->insert_id;
        //Insert Wp user Id and last term id to wp_wk_users_taxonomy.
        $table_name = $wpdb->prefix . "wk_users_taxonomy";
        $wpdb->insert($table_name, array(

            'term_id' => Functions::get_last_term_id(),
            'user_id' => Functions::get_last_registered_user()->ID,

        ));
        //Insert data to term_taxonomy.
    	$table_name = $wpdb->prefix . "term_taxonomy";

    	if (is_admin()) require_once(ABSPATH . 'wp-includes/pluggable.php');

    	$user_meta = get_userdata(Functions::get_last_registered_user()->ID);

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
    function delete_wp_user_weekly_data($user_id){
    
        global $wpdb;

        $term_id = Functions::get_wk_users_taxonomy_by_user_id($user_id);

        $term_deleted = false;
        if(!is_null($term_id) && is_numeric($term_id)){

            $term = get_term( $term_id );
            if($term->taxonomy == 'wcs-instructor'){
                $term_deleted = wp_delete_term( $term_id, 'wcs-instructor');
            }
            else{
                $term_deleted = wp_delete_term( $term_id, 'wcs-student');
            }            
        }
        
        if($term_deleted){

            $table_name = $wpdb->prefix . "wk_users_taxonomy";

            $wpdb->delete($table_name , array(

              'user_id' => $user_id
            ));
        }

    
    }
}