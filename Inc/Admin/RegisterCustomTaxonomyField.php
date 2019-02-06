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
use Fajr\CustomWeeklyClass\Base\AddCustomField;

class RegisterCustomTaxonomyField extends AddCustomField
{
	
	function register()
	{

		$student_field = new AddCustomField();
		$student_field->taxonomy_name = 'wcs-student';
		$student_field->html_tag_name = 'wp_student';
		$student_field->user_role = '\'%student%\'';
		$student_field->register();

		$instractur_field = new AddCustomField();
		$instractur_field->taxonomy_name = 'wcs-instructor';
		$instractur_field->html_tag_name = 'wp_instructor';
		$instractur_field->user_role = '\'%instructor%\'';
		$instractur_field->register();


	}
}