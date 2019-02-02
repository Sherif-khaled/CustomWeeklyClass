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

class AddTaxonomy{

	public function register(){
		add_action( 'init', array($this, 'student_taxonomy' ));

	}

	function student_taxonomy(){

		$labels = array(
            'name'                       => _x( 'Students', 'taxonomy general name', 'WeeklyClass' ),
            'singular_name'              => _x( 'Students', 'taxonomy singular name', 'WeeklyClass' ),
            'search_items'               => __( 'Search  Students', 'WeeklyClass' ),
            'popular_items'              => __( 'Popular Students', 'WeeklyClass' ),
            'all_items'                  => __( 'All Students', 'WeeklyClass' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Students', 'WeeklyClass' ),
            'update_item'                => __( 'Update Students', 'WeeklyClass' ),
            'add_new_item'               => __( 'Add New Student', 'WeeklyClass' ),
            'new_item_name'              => __( 'New Students Name', 'WeeklyClass' ),
            'separate_items_with_commas' => __( 'Separate Students with commas', 'WeeklyClass' ),
            'add_or_remove_items'        => __( 'Add or remove Students', 'WeeklyClass' ),
            'choose_from_most_used'      => __( 'Choose from the most used Students', 'WeeklyClass' ),
            'not_found'                  => __( 'No Students found.', 'WeeklyClass' ),
            'menu_name'                  => __( 'Students', 'WeeklyClass' )
        );

        $labels = apply_filters( 'wcs_tax_labels', $labels, 'wcs-student' );

        $args = array(
            'hierarchical'          => false,
            
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => false,
            'wcs_icon'          => 'ti-location',
            'wcs_labels'        => array(
            'all'   => __( 'All', 'WeeklyClass' )
      )
        );

        if( wp_validate_boolean( $settings['wcs_classes_archive'] ) ) unset( $args['query_var'] );

        register_taxonomy( 'wcs-student', 'class', $args );
	}

}