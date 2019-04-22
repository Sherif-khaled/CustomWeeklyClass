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
class EnrollUsers{

	public function register(){
        add_action('publish_class', array($this,'enroll_users_to_course'),10,2);
	}
	function test($data){

        var_dump($data);
	}

	function enroll_users_to_course($ID, $post){

		$course_data = $this->get_schedule_course_details($ID);

		$this->add_user_to_course($course_data, $course_data['wplms_course_id']);

		return true;

	}
	//this function will fire after publish scadulae class

    /**
     * @param $post_id
     * @return array
     */
  function get_schedule_course_details($post_id){
		$post = get_post_meta($post_id);

        $wcs_duration = $post['_wcs_duration'][0];

        $wcs_start_date = date('d/m/Y',$post['_wcs_timestamp'][0]);

        $wcs_start_time = date('h:m',$post['_wcs_timestamp'][0]);

        $wcs_end_time = $this->timestamp_to_date($post_id);

        $wcs_end_date = date('d/m/Y',strtotime($post['_wcs_repeat_until'][0]));

        $wcs_repeat_days = $this->get_wcs_repeat_days($post_id);

        $students = get_the_terms($post_id,'wcs-student');

        $wcs_course_id = $post['wplms_course'][0];

		$wcs_students = array();

		foreach( $students as  $value ){
			global $wpdb;

		    $table_name = $wpdb->prefix . "wk_users_taxonomy";
			$users_id =  $wpdb->get_results( "SELECT user_id FROM $table_name WHERE term_id = $value->term_id" );

			array_push($wcs_students, $users_id[0]->user_id);
        }

        $instructors = get_the_terms($post_id,'wcs-instructor');

        $wcs_instructors = array();
		foreach( $instructors as  $value ){
			global $wpdb;

		    $table_name = $wpdb->prefix . "wk_users_taxonomy";
			$users_id =  $wpdb->get_results( "SELECT user_id FROM $table_name WHERE term_id = $value->term_id" );

			array_push($wcs_instructors, $users_id[0]->user_id);
        }

        $wcs_schedule_data = array('students' => $wcs_students,
                                    'instructors' => $wcs_instructors,
                                    'start_date' => $wcs_start_date,
                                    'start_time' => $wcs_start_time,
                                    'end_time' => $wcs_end_time,
                                    'end_date' => $wcs_end_date,
                                    'repeat_days' => $wcs_repeat_days,
                                    'wcs_duration' => $wcs_duration,
                                    'wplms_course_id' => $wcs_course_id);

        return $wcs_schedule_data;


	}
	function add_user_to_course($users, $course_id){
		if(is_array($users) && !is_null($course_id)){
			if(count($users) != 0){

				//Enroll Students to WPLMS course.
		        foreach ($users['students'] as $value) {
		        	bp_course_add_user_to_course(intval($value),intval($course_id));
		        }
		         //Enroll instructors to WPLMS course.
		        foreach ($users['instructors'] as $value) {
		        	bp_course_add_user_to_course(intval($value),intval($course_id));
		        }

			}
		}
	}
	function add_user_to_patch($user_id, $course_id){

		//Get User Gender
		$user_gender = get_the_author_meta('user_gender',$user_id);

		//check if groups count > 0
		$patches_count = count($this->get_patches());
		if($patches_count > 0){

			if(count($this->get_course_batches($course_id)) > 0){

			}
			$patch_exist = BP_Groups_Group::group_exists('male-group');

		}else{
			//create (Male,Female) Patches and set first course and user to patch

			$male = $this->create_patch('Male','public','Primary Patch',$course_id);
			$female = $this->create_patch('Female','public','Praimary Patch',$course_id);

			if($user_gender == 'male'){
				$this->set_user_to_patch($male, $user_id);

			}elseif ($user_gender == 'female') {
				$this->set_user_to_patch($female, $user_id);
			}

		}

		//check if course have patches called (male,female)

		//

	}
	function set_user_to_patch($group_id,$user_id){
		global $wpdb,$bp;

		$is_admin = 0;
		$user_title = '';

		if($this->is_admin($user_id)){
			$is_admin = 1;
			$user_title = 'Group Admin';
		}

		$result = $wpdb->insert($bp->groups->table_name_members,array('group_id' => $group_id,
			                                                          'user_id' => $user_id,
			                                                          'inviter_id' => 0,
	                                                                  'is_admin' => $is_admin,
	                                                                  'user_title' => $user_title,
	                                                                  'date_modified' => date("Y-m-d H:i:s"),
	                                                                  'comments' => '',
	                                                                  'is_confirmed' => 1
	                                                                ));
		return $result;

	}
	function set_course_to_patch($group_id,$course_id){
		global $wpdb,$bp;

		$result = $wpdb->insert($bp->groups->table_name_groupmeta,array('group_id' => $group_id,
			                                                            'meta_key' => 'batch_course',
	                                                                    'meta_value' => $course_id));
		return $result;
	}
	function get_patches(){
		global $wpdb;

		$groups_table_name = $wpdb->prefix . 'bp_groups';

		$meta_groups_table_name = $wpdb->prefix . 'bp_groups_groupmeta';

		$batches = $wpdb->get_results("SELECT g.id , g.name, g.slug FROM {$groups_table_name} as g
			                          LEFT JOIN  {$meta_groups_table_name} AS gm
			                          ON g.id = gm.group_id
			                          WHERE gm.meta_key = 'course_batch'");

		return $batches;
	}
	function get_batch_courses($batch_id){

		if(!empty($this->courses[$batch_id])){ // IF already set
			return $this->courses[$batch_id];
		}

		$this->courses[$batch_id] = groups_get_groupmeta($batch_id,'batch_course',false);

		if(!empty($this->courses[$batch_id])){
			return $this->courses[$batch_id];
		}
		return 0;
	}
	function get_course_batches($course_id){

		if(!empty($this->course_batches[$course_id]))
			return $this->course_batches[$course_id];

		global $wpdb,$bp;
		$this->course_batches[$course_id] = $wpdb->get_results($wpdb->prepare("SELECT group_id FROM {$bp->groups->table_name_groupmeta} WHERE meta_key = %s AND meta_value = %d",'batch_course',$course_id));

		return $this->course_batches[$course_id];
	}
	function is_admin($user_id){
		$user = get_userdata( $user_id );
		if(!empty( $user ) && $user){
		   $user->roles ;// this contains the role here check for whatever role you need
		}
		$role = $user->roles[0];

		if($role != 'administrator'){
			return false;
		}
		return true;
	}
	function create_batch($name, $status,$desc = '',$course_id = 0){
        $user_id = get_current_user_id();


        $course_id = intval($course_id);
        $group_settings = array(
            'creator_id' => $user_id,
            'name' => $name,
            'description' => '',
            'status' => $status,
            'date_created' => current_time('mysql')
        );

        global $bp;

        $new_group_id = groups_create_group( $group_settings);

        if(is_numeric($new_group_id)){
            groups_update_groupmeta( $new_group_id, 'total_member_count', 1 );
            groups_update_groupmeta( $new_group_id, 'last_activity', gmdate( "Y-m-d H:i:s" ) );

            groups_update_groupmeta( $new_group_id, 'course_batch',1);
            if($course_id <> 0){
            	groups_add_groupmeta($new_group_id,'batch_course',$course_id);
            }

			$group = groups_get_group( array('group_id' => $new_group_id,'populate_extras'   => false,'update_meta_cache' => false) );
        }
        return $new_group_id;


	}

    function get_bbb_session_data($weekdays, $post_id){
        $session_data =(object) array('start_date' => $this->get_list_of_session_dates($weekdays , $post_id),
            'meeting_time' => $this->get_schedule_course_details($post_id)['start_time'],
            'meeting_duration' => $this->get_schedule_course_details($post_id)['wcs_duration']);
        return $session_data;

    }
    function timestamp_to_date($post_id){
        $duration   = get_post_meta( $post_id, '_wcs_duration', true );

        $timestamp  = get_post_meta( $post_id, '_wcs_timestamp', true );

        $end = $timestamp + ($duration * MINUTE_IN_SECONDS);

        $end_time = date('H:m', $end);

        return $end_time;
    }
    /**
     * Get List Of Session Dates For Class.
     * @uses $this->get_wcs_repeat_days()
     * @param $weekdays
     * @param $post_id
     * @return array $days_date
     */
    function get_list_of_session_dates($weekdays , $post_id){
        if(!is_array($weekdays)){
            throw new Exception("The \$weekdays parameter Must be Array.");
        }

        $post = get_post_meta($post_id);

        //Get Class Start Date
        $wcs_start_date = date('d-m-Y',$post['_wcs_timestamp'][0]);

        //Get Class Repeat End Date
        $wcs_repeat_end_date  = date('d-m-Y',strtotime($post['_wcs_repeat_until'][0]));

        //Convert $wcs_start_date To  DateTime
        $begin = new DateTime( $wcs_start_date );

        //Convert $wcs_repeat_end_date To  DateTime
        $end = new DateTime( $wcs_repeat_end_date );

        $end = $end->modify( '+1 day' );

        //Set Peer One Day Interval "P1D"
        $interval =  new DateInterval('P1D');

        //Set Date Range To Accept From Start Date To End Date;
        $date_range =  new DatePeriod($begin, $interval ,$end);

        $days_date = array();

        //Get Class Repeat Name Day Of week
        $week_days  = $this->get_wcs_repeat_days($post_id);

        foreach($date_range as $date){

            $next_day = $date->format("D");
            foreach ($week_days as $key=>$value) {
                if (strpos( strtolower($value), strtolower($next_day)) !== false) {
                    array_push($days_date , $date->format("d-m-Y"));
                }
            }
        }

        return $days_date;

    }
    //Get Class Repeat Name Day Of week
    function get_wcs_repeat_days($post_id){
        $start_of_week = intval( get_option( 'start_of_week', 0 ) );

        $weekdays_ini = array(0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday');

        $repeat_days = get_post_meta( $post_id, '_wcs_repeat_days', true );
        $repeat_days = $repeat_days && ! empty( $repeat_days ) ? $repeat_days : array( 0,1,2,3,4,5,6 );
        $wcs_repeat_days = array();
        for ($i = $start_of_week; $i <= $start_of_week + 6; $i++): $key = $i <= 6 ? $i : $i - 7;
            if(in_array($key, $repeat_days)){
                array_push($wcs_repeat_days,$weekdays_ini[$key]);
            }

        endfor;
        return $wcs_repeat_days;
    }

    /**
     * Get Bigbluebutton Sessions From Course Unit
     * @param post_id
     * @return matches Array Of Bigbluebutton Sessions Id
     */
    function get_bbb_from_unit($post_id){

        $unit = get_post_field('post_content',$post_id);

        //Extract Array of Bigbluebutton Sessions Id
        return $this->extract_id_string($unit);

    }
    /**
     * Extract Bigbluebutton Sessions id from string
     * @param $str
     * @return matches
     */
    function extract_id_string($str){
        $re = '/[a-z0-9]{12}+/';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        return $matches;
    }

}
