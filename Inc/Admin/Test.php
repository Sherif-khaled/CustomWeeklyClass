<?php

namespace Fajr\CustomWeeklyClass\Admin;

class Test
{
    public function register(){
      // add_action( 'init', array($this,'test') );

    }

    function test(){
        $test = $this->get_course_batches(131);
        var_dump($test);
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
    function get_course_batches($course_id){
        global $wpdb;
        $groups_table_name = $wpdb->prefix . 'bp_groups';

        $meta_groups_table_name = $wpdb->prefix . 'bp_groups_groupmeta';

       $course_batches = $wpdb->get_results("SELECT g.id,g.name FROM {$groups_table_name} as g
                                                   LEFT JOIN {$meta_groups_table_name} as gm
                                                   on g.id = gm.group_id
                                                   WHERE meta_key = 'batch_course' 
                                                   AND meta_value = {$course_id}");
return $course_batches;
//        $str = "";
//        foreach ($course_batches as $batch){
//            $str = $str . "<option>$batch</option>";
//        }
//        var_dump( $str);
    }

}