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
class Functions extends BaseController
{
	/**
	 * Get list of Wordpress users by user role
	 * @param  $role Wordpress User role
	 * @return  $users  list of wordpress users
	 */
	
	function get_WP_users($role){

	    $args = array(
	                        'orderby'      => 'registered',
	                        'order'        => 'DESC',  
	                        'role'         => $role       
	                    );
	    $users = get_users( $args );

	    $users = $users;

	    return $users;
    }
    function get_WP_users_using_quary($role){

    	global $wpdb;
	    $table_name = $wpdb->prefix . "users";

	    $users = $wpdb->get_results( "SELECT ID FROM $table_name INNER JOIN wp_usermeta 
	    	                          ON wp_users.ID = wp_usermeta.user_id
	    	                          WHERE wp_usermeta.meta_key = 'wp_capabilities' 
				                      AND wp_usermeta.meta_value LIKE $role " );

	    $users_array = array();

	    foreach ($users as $value) {

            $value = json_decode(json_encode($value), True);
            
            array_push($users_array, $value);
          
        }

        return $users_array;
    }
    /**
     * Get user data after WP user register.
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
    function get_wk_users_taxonomy(){

    	global $wpdb;

    	$table_name = $wpdb->prefix . "wk_users_taxonomy";

    	$result = $wpdb->get_results( "SELECT user_id FROM $table_name" );

    	return $result;
    }
    function get_wk_users_taxonomy_by_term_id($value){

        global $wpdb;

        $table_name = $wpdb->prefix . "wk_users_taxonomy";

        $result = $wpdb->get_results( "SELECT user_id FROM $table_name WHERE term_id = $value" );

        return $result[0]->user_id;
    }
    function get_wk_users_taxonomy_by_user_id($value){

        global $wpdb;

        $table_name = $wpdb->prefix . "wk_users_taxonomy";

        $result = $wpdb->get_results( "SELECT term_id FROM $table_name WHERE user_id = $value" );

        return $result[0]->term_id;
    }
    function get_avalibal_wp_users($role){

    	$wp_users = $this->get_WP_users_using_quary($role);

        $wp_users = $this->recursive_change_key($wp_users, array('ID' => 'user_id'));

        $taxonomy_users = $this->get_wk_users_taxonomy();

        $tax_users = array();
        $tax_users = json_decode(json_encode($taxonomy_users), True);

        $avalibale_users = $this->check_diff_multi_array($wp_users,$tax_users);

        $id_value = array_column($avalibale_users, 'user_id');

        $wp_users_data = array();
        foreach ($id_value as $value){

        	$user = get_user_by( 'ID',(int)$value);
       
        	array_push($wp_users_data, $user);
        }
 
         return $wp_users_data;

    }
    function get_last_term_id(){

        global $wpdb;
        $table_name = $wpdb->prefix . "terms";

        $term_id = $wpdb->get_results( "SELECT MAX(term_id) FROM $table_name" );

        $g = $term_id[0];

        return $g->{'MAX(term_id)'};
    }
    /**
     *  Change array key
     *  @param $arr => original array
     *  @param $set => array containing old keys as keys and new keys as values
     *  @return new array with the new key 
     */
    function recursive_change_key($arr, $set) {

        if (is_array($arr) && is_array($set)) {

    		$newArr = array();

    		foreach ($arr as $k => $v) {

    		    $key = array_key_exists( $k, $set) ? $set[$k] : $k;

    		    $newArr[$key] = is_array($v) ? $this->recursive_change_key($v, $set) : $v;
    		}

    		return $newArr;
    	}

    	return $arr;    
    }
    /**
     * Return  the difference between two multidimensional array.
     * @param  $array1
     * @param  $array2
     * @return $array1
     */
    function check_diff_multi_array($array1, $array2){

        foreach ($array1 as $key => $value) {

	        if (in_array($value, $array2)) {

	            unset($array1[$key]);
	        }
        }

        return $array1;
    }
}