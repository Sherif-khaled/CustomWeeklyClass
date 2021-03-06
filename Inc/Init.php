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


namespace Fajr\CustomWeeklyClass;

final class Init{

	/**
	 * Store all the classes inside an array.
	 * @return array full list of classes
	 */
	public static function get_services(){
		return [
			Admin\AddTaxonomy::class,
			Admin\RegisterWPLMSUser::class,
			Admin\RegisterCustomTaxonomyField::class,
			Admin\AddMetaBox::class,
			Admin\EnrollUsers::class,
			Base\Enqueue::class,
            Admin\Test::class,
		];
	}
	/**
	 * Loop through the classes, initialize them,
	 * it exist and call the register() method if
	 * @return
	 */
	public static function register_service(){
		foreach (self::get_services() as $class) {
			$service = self::instantiate( $class );
			if(method_exists($service, 'register')){
				$service->register();
			} 
		}
	}
	/**
	 * initialize the class.
	 * @param class $class        class from the services array
	 * @return class instance  the new instance of the class.
	 */
	private static function instantiate($class){
		$service = new $class();
		return $service;
	}
}