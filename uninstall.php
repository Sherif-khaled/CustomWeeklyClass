<?php

/**
 *Trigger this file on plugin uninstall.
 *
 * @package x_book_Plugin
 */

if (! defined('WP_UNINSTALL_PLUGIN')){
	die;
}

// //Methos : 1 ** Clear database Stored data.
// $books = get_posts( 'post_type' => 'x_book' , 'numberposts' => -1 );

// foreach ($books as $book) {
// 	wp_delete_post( $book->ID, true );
// }
	

//Method : 2 ** Access the database via SQL -- the best function
global $wpdb;

$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'x_book'")	;

$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts)");

$wpdb->query("DELETE FROM wp_term_relationships WHERE post_id NOT IN (SELECT id FROM wp_posts)");