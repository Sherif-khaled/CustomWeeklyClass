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
use Fajr\CustomWeeklyClass\Base\Functions;

class AddCustomField extends Functions
{
	public $taxonomy_name;

	public $html_tag_name;

	public $user_role;	
	
	function register()
	{

		add_action( "{$this->taxonomy_name}_add_form_fields", array($this,'taxonomy_custom_fields'), 10, 2 );
	    add_action( "{$this->taxonomy_name}_edit_form_fields", array($this,'taxonomy_custom_fields'), 10, 2 );

	    //add_action( "create_{$this->taxonomy_name}" ,array($this,'save_taxonomy_custom_meta_field'), 10, 2 );
	    
	    add_action( 'wp_ajax_save_taxonomy_custom_meta_field',array($this, 'save_taxonomy_custom_meta_field' ));
	    add_action('wp_ajax_nopriv_save_taxonomy_custom_meta_field', array($this, 'save_taxonomy_custom_meta_field'));

         /**
		 * TODO: OPTIMIZE THE FUNCTION TO ADD USER NAME TO SELECT LIST 
		 * AFTER REMOVE FROM TAXOXOMY.
		 */
	    // add_action( 'wp_ajax_delete_taxonomy_custom_meta_field',array($this, 'delete_taxonomy_custom_meta_field' ));
	    // add_action('wp_ajax_nopriv_delete_taxonomy_custom_meta_field', array($this, 'delete_taxonomy_custom_meta_field'));

	    add_action( "edited_{$this->taxonomy_name}" ,array($this,'save_taxonomy_custom_meta_field'), 10, 2);

	    add_action( "delete_{$this->taxonomy_name}",array($this ,'delete_taxonomy_custom_meta_field'), 10, 2);

	    
 
	}
	function taxonomy_custom_fields(){
       include $this->plugin_path . 'template/view.php';  
	}
	function save_taxonomy_custom_meta_field( $term_id) {

	    if ( isset( $_POST['myselect'] ) ) {   

            $option_name = $this->html_tag_name . '_' . $term_id;
	        update_option( $option_name, $_POST[$this->html_tag_name] );
	
			global $wpdb;

	        $table_name = $wpdb->prefix . "wk_users_taxonomy";

	        $wpdb->insert($table_name,array(

	            'term_id' => Functions::get_last_term_id(),
	            'user_id'  => $_POST['myselect'],
	            ));

	        die();     
	    }
	}
	function delete_taxonomy_custom_meta_field($term_id){

		global $wpdb;

		$table_name = $wpdb->prefix . "wk_users_taxonomy";

		$wpdb->delete($table_name , array(

			  'term_id' => $term_id
			));
		/**
		 * TODO: OPTIMIZE THE FUNCTION TO ADD USER NAME TO SELECT LIST 
		 * AFTER REMOVE FROM TAXOXOMY.
		 */
		// $user_id = Functions::get_wk_users_taxonomy_by_term_id($term_id);

		// $user = get_user_by('ID',$user_id);

		// $display_name = $user->data->display_name;

		// die();

	}
    
}
