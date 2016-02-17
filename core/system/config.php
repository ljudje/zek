<?php

class Config {

	private static $instance = null;
	public static $config = array ();

	public function __construct ($config)
	{
		self::$config = $config;
	}

	static function getInstance ($config = 'config')
	{
		if (!self::$instance) {
			self::$instance = new self ($config);
		}
		return self::$config;
	}

	static function is ($key)
	{
		return isset (self::$config[$key]);
	}

	static function get ($key)
	{
		if (self::is ($key)) {
			return self::$config[$key];
		}
	}

	static function set($key, $value)
	{
		self::$config[$key] = $value;
	}

	static function remove($key)
	{
		unset(self::$config[$key]);
	}

}

$config = Config::getInstance ($config);
Config::set('config', $config);
