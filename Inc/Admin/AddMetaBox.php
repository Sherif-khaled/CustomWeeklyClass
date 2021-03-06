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

namespace Fajr\CustomWeeklyClass\admin;
use Fajr\CustomWeeklyClass\Base\Functions;
class AddMetaBox extends Functions{

	function register(){
				add_action( 'add_meta_boxes', [$this,'add_class_metabox'] );
				add_action( 'save_post', array($this,'save_wplms_course_custom_meta_field'), 1, 2 );

//        add_action( 'add_meta_boxes', array($this,'add_batch_metabox') );
//        add_action( 'save_post', [$this,'save_wplms_batch_custom_meta_field'], 1, 2 );

        add_action( 'wp_ajax_get_course_batches',array($this, 'get_course_batches' ));
        add_action('wp_ajax_nopriv_get_course_batches', array($this, 'get_course_batches'));


        //Create User Gender Field
				add_action( 'show_user_profile', array($this,'user_profile_gender') );
				add_action( 'edit_user_profile', array($this,'user_profile_gender') );
				add_action( "user_new_form", array($this,'user_profile_gender') );
                //Save User Gender Field
				add_action( 'personal_options_update', array($this, 'save_user_profile_gender_fields' ));
				add_action( 'edit_user_profile_update', array($this, 'save_user_profile_gender_fields' ));
				add_action( 'user_register', array($this, 'save_user_profile_gender_fields' ));
				
	}
	
	/**
	 * Adds a metabox to the right side of the screen under the â€œPublishâ€ box
	 */
	function add_class_metabox() {
		add_meta_box(
			'wplms_courses',
			'WPLMS Courses',
			array($this,'wpt_wplms_courses_view'),
			'class',
			'normal',
			'high'
		);
	}
//    function add_batch_metabox() {
//        add_meta_box(
//            'wplms_batches',
//            'WPLMS Batches',
//            array($this,'wpt_wplms_courses_view'),
//            'class',
//            'normal',
//            'high'
//        );
//    }
	/**
	 * Output the HTML for the metabox.
	 */
	function wpt_wplms_courses_view() {
		global $post;
		// Nonce field to validate form request came from current site
		wp_nonce_field( basename( __FILE__ ), 'wplms_course_fields' );
		// Get the location data if it's already been entered
		$wplms_course = get_post_meta( $post->ID, 'wplms_course', true );
		// Output the field
		
	    ?>
		<div class="form-group">
            <label class="name" name='courses_label' for='wplms_course' >Select WPLMS Course</label>
            <div>
                <select class="form-control tttt" id ='wplms_course' name='wplms_course' required>
                    <option value="" selected disabled hidden>Select Here</option>

                    <?php
                    foreach (AddMetaBox::bp_get_all_publish_courses() as $course) {

                        echo "<option ' value='" . esc_html( $course['id'] ) . "' >" . $course['name'] . "</option>\n";

                    }
                    ?>

                </select>

            </div>
        </div>
       <div style="margin-top:10px" class="form-group">
           <label class="name" name='patches_label' for='patch' >Select Batch</label>
           <div>
               <select class="form-control" id ='patch' class='target' name='batch' required>
                   <option value="" selected disabled hidden>Select Here</option>

                   <?php
                   $test = AddMetaBox::get_course_batches(128);
                   foreach ($test as $batch) {

                       echo "<option ' value='" . esc_html( $batch->id ) . "' >" . $batch->name . "</option>\n";

                   }
                   ?>

               </select>
           </div>
       </div>

<style>
    .name{
        font-weight: 700;
    }
</style>
        <?php
    }

    /**
     * @return array
     */
    function bp_get_all_publish_courses(){
		global $wpdb;

		$query = $wpdb->get_results("
	    	  SELECT DISTINCT posts.ID as id,posts.post_name as name
		      FROM {$wpdb->posts} AS posts
		      LEFT JOIN {$wpdb->usermeta} AS meta ON posts.ID = meta.meta_key
		      WHERE  posts.post_status = 'publish' AND posts.post_type = 'course'
		      ORDER BY ID ASC");

		$wplms_courses =array();
	  	if(isset($query) && is_array($query)){
	    	foreach($query as $q){	
	    		$course = array('id' => $q->id , 'name' => $q->name);
	    		array_push($wplms_courses, $course);    		
	    	} 
	  	}

  	    return $wplms_courses;
	      
	}
    function get_course_batches($course_id){
        global $wpdb;
        $groups_table_name = $wpdb->prefix . 'bp_groups';

        $meta_groups_table_name = $wpdb->prefix . 'bp_groups_groupmeta';

        $course_batches = $wpdb->get_results("SELECT g.id,g.name FROM {$groups_table_name} as g
                                                   LEFT JOIN {$meta_groups_table_name} as gm
                                                   on g.id = gm.group_id
                                                   WHERE meta_key = 'batch_course' 
                                                   AND meta_value = {$course_id}");

//        if (wp_doing_ajax()) {
//
//            $str = "";
//            foreach ($course_batches as $batch){
//                $str = $str . "<option>$batch</option>";
//            }
//            echo $str;
//            die();
//
//        }
        return $course_batches;

    }

    /**
     * @param $post_id
     * @param $post
     */
    function save_wplms_course_custom_meta_field($post_id, $post) {

		   // Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.
		if ( ! isset( $_POST['wplms_course'] ) || ! wp_verify_nonce( $_POST['wplms_course_fields'], basename(__FILE__) ) ) {
			return $post_id;
		}
		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$course_meta['wplms_course'] = esc_textarea( $_POST['wplms_course'] );
		// Cycle through the $events_meta array.
		// Note, in this example we just have one item, but this is helpful if you have multiple.
		foreach ( $course_meta as $key => $value ) :
			// Don't store custom data twice
			if ( 'revision' === $post->post_type ) {
				return;
			}
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value);
			}
			if ( ! $value ) {
				// Delete the meta key if there's no value
				delete_post_meta( $post_id, $key );
			}
		endforeach;
	}


    /**
     * Output the User Gender Field HTML for the metabox.
     * @param $user
     */
	function user_profile_gender($user) {
		
		$user_gender = get_the_author_meta( 'user_gender', $user->ID);
       ?>
			<h3><?php _e("Custom User information", "blank"); ?></h3>
			<table class="form-table">
		    <tr>
		        <th>
		            <label for="user_gender"><?php _e("Gender"); ?></label>
		        </th>
		        <td>
		            <select name="user_gender" id="user_gender" style="width:180px" required>
		            	<option value="">-Select Gender-</option>
		                <option value="male"   <?php selected( $user_gender, "male"); ?>>Male</option>
		                <option value="female" <?php selected( $user_gender, "female"); ?>>Female</option>
		            </select>
		        </td>
		    </tr>
		</table>
        <?php 
    }

    /**
     * Save User Gender Field.
     * @param $user_id
     * @return bool
     */
   function save_user_profile_gender_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		update_user_meta( $user_id, 'user_gender', $_POST['user_gender'] );
	}


}



//https://wptheming.com/2010/08/custom-metabox-for-post-type/