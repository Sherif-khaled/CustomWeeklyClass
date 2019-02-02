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

class AddCustomField{

	public function register(){
		add_action('user_register', array($this, 'save_custom_user_profile_fields'));
        add_action('profile_update', array($this,'save_custom_user_profile_fields'));

        add_action( 'show_user_profile', array($this,'custom_user_profile_fields' ));
	    add_action( 'edit_user_profile', array($this,'custom_user_profile_fields' ));
	    add_action( 'user_new_form', array($this,'custom_user_profile_fields' ));

	    add_action( 'category_add_form_fields', array($this,'taxonomy_add_new_meta_field'), 10, 2 );
	    add_action( 'wcs-student_add_form_fields', array($this,'taxonomy_add_new_meta_field'), 10, 2 );
	}
	function custom_user_profile_fields($user){
		?>
			<h3>Extra profile information</h3>
			<table class="form-table">
			    <tr>
					<th><label for="timetable">Add user to Weekly Class</label></th>
					    <td>
					        <input type="checkbox" class="regular-text" name="timetable" value="<?php echo esc_attr( get_the_author_meta( 'Add User To weeklyclass', $user->ID ) ); ?>" id="timetable" />
					        <span class="description">Register the new user to Weekly Class.</span>
					    </td>
					</tr>
			</table>
		<?php
	}
	function save_custom_user_profile_fields($user_id){
		# again do this only if you can
		if(!current_user_can('manage_options'))
		    return false;

		# save my custom field
		update_usermeta($user_id, 'weeklyclass', $_POST['timetable']);
	}
	function get_WP_users(){

	    $args = array(
	                        'orderby'      => 'registered',
	                        'order'        => 'DESC',  
	                        'role'         => 'student'       
	                    );
	    $students = get_users( $args );

	    return $students;
    }
    function taxonomy_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>
        <label for='wp_students'>Wordpress Studens</label>
        <select name='wp_students' id='post_theme'>
            <!-- Display Students as options -->
             
                <?php
            foreach ($this->get_WP_users() as $student) {
               
             echo "<option class='theme-option' value='" . esc_html( $student->data->display_name ) . "' selected>" .esc_html( $student->data->display_name ) . "</option>\n"; 
              
            }

           ?>
        </select> 
   
        <?php
    }
    // Save extra taxonomy fields callback function.
	function save_taxonomy_custom_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "taxonomy_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			// Save the option array.
			update_option( "taxonomy_$t_id", $term_meta );
		}
	}  
	add_action( 'edited_category', 'save_taxonomy_custom_meta', 10, 2 );  
	add_action( 'create_category', 'save_taxonomy_custom_meta', 10, 2 );

}


