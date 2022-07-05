<?php
moduleMenuAutoloader::register();

class moduleMenuAutoloader
{
	/**
	* Register the Autoloader with SPL
	*
	*/
	public static function register()
	{
		if (function_exists('__autoload')) {
			// Register any existing autoloader function with SPL, so we don't get any clashes
			spl_autoload_register('__autoload');
		}
		// Register ourselves with SPL
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			return spl_autoload_register(array('moduleMenuAutoloader', 'load'), true, true);
		} else {
			return spl_autoload_register(array('moduleMenuAutoloader', 'load'));
		}
	}
	
	/**
	 * Autoload a class identified by name
	 *
	 * @param    string    $pClassName        Name of the object to load
	 */
	public static function load($pClassName)
	{
		$dirs = [
			'application/libraries/moduleMenu',
		];

		foreach ($dirs as $dir) {
			$pClassFilePath = $dir . '/' . $pClassName . '.php';
			if ((file_exists($pClassFilePath) === true) ) {
				require($pClassFilePath);
			}	
		}
	}
}
