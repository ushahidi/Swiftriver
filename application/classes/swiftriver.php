<?php defined('SYSPATH') or die('No direct script access');
/**
 * Initializes the SwiftRiver environment
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Cookie config
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Swiftriver {

	/**
	 * Default salt value to add to the cookies
	 */
	const DEFAULT_COOKIE_SALT = 'cZjO0Lgfv7QrRGiG3XZJZ7fXuPz0vfcL';

	// Cookie name constants
	const COOKIE_SEARCH_SCOPE = "search_scope";
	const COOKIE_PREVIOUS_SEARCH_SCOPE = "previous_search_scope";
	const COOKIE_SEARCH_ITEM_ID = "search_item_id";

	/**
	 * Available content filtering options
	 * @var array
	 */
	private static $content_filters = array();

	/**
	 * Application initialization
	 *     - Loads the plugins
	 *     - Sets the cookie configuration
	 */
	public static function init()
	{
		// Set defaule cache configuration
		Cache::$default = Kohana::$config->load('site')->get('default_cache');
		
		try
		{
			$cache = Cache::instance()->get('dummy'.rand(0,99));
		}
		catch (Exception $e)
		{
			// Use the dummy driver
			Cache::$default = 'dummy';
		}
		
		
		// Load the plugins
		Swiftriver_Plugins::load();

		// Add the current default theme to the list of modules
		$theme = Model_Setting::get_setting('site_theme');

		if ($theme != "default")
		{
			Kohana::modules(array_merge(
				array('themes/'.$theme->value => THEMEPATH.$theme->value),
				Kohana::modules()
			));
		}

		// Clean up
		unset ($active_plugins, $theme);

		// Load the cookie configuration
		$cookie_config = Kohana::$config->load('cookie');
		Cookie::$httponly = TRUE;
		Cookie::$salt = $cookie_config->get('salt', Swiftriver::DEFAULT_COOKIE_SALT);
		Cookie::$domain = $cookie_config->get('domain') OR '';
		Cookie::$secure = $cookie_config->get('secure') OR FALSE;
		Cookie::$expiration = $cookie_config->get('expiration') OR 0;

		// Set the default site locale
		I18n::$lang = Model_Setting::get_setting('site_locale');
	}
	
	/**
	 * Returns the CDN url for $file
	 *
	 * @param   string   file name
	 * @return  string
	 */
	public static function get_cdn_url($file)
	{
		$cdn_url = Kohana::$config->load('site')->get('cdn_url');
		if (isset($cdn_url))
		{
			$cdn_dirs = Kohana::$config->load('site')->get('cdn_directories');
			foreach ($cdn_dirs as $dir)
			{
				$file = preg_replace('|^('.$dir.')|', $cdn_url.'/$1', $file);
			}
		}
		
		return $file;
	}


	/**
	 * Gets the list of available content filters and returns 
	 * each a list of key => value arrays for each filter. These filters
	 * can be used at the river or bucket level
	 *
	 * @return array
	 */
	public static function get_content_filters()
	{
		if ( ! empty(self::$content_filters))
			return self::$content_filters;

		$config_data = Kohana::$config->load('filters');

		$filters = array();
		foreach ($config_data as $filter => $config)
		{
			self::$content_filters[] = array(
				'filter' => $filter,
				'options' => array(
					'name' => $config['name'],
					'label' => $config['label'],
					'placeholder' => $config['placeholder'],
					'type' => $config['type']
				)
			);
		}

		return self::$content_filters;
	}

	/**
	 * Given the name of a filter, returns its configuration
	 *
	 * @param  string  $filter_name Name of the filter
	 * @return mixed   array when the filter exists, FALSE otherwise 
	 */
	public static function get_content_filter_config($filter_name)
	{
		$filters = self::get_content_filters();

		foreach ($filters as $entry)
		{
			if ($entry['filter'] === $filter_name)
				return $entry['options'];
		}

		return FALSE;
	}

}
