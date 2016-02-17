<?php

class Autoload {

	private static $methods  = array();

	public static function load ($className)
	{
		$filename = strtolower (preg_replace('/([a-z0-9])([A-Z])/', '\\1_\\2', $className));
		foreach (self::$methods as $method) {
			$file = call_user_func ($method, $filename);
			if (is_file ($file)) {
				return include ($file);
			}
		}
	}

	public static function register($method)
	{
		if (!in_array($method, self::$methods)) {
			array_unshift(self::$methods, $method);
		}
	}

	public static function loadWireframe ($filename)
	{
		return Config::get("wireframe_dir") . preg_replace ("/wireframe_/i", "", $filename) . "/" . $filename . ".php";
	}

	public static function loadWireframeModel ($filename)
	{
		return Config::get("wireframe_dir") . preg_replace ("/wireframe_|_model/i", "", $filename) . "/" . $filename . ".php";
	}

	public static function loadModule ($filename)
	{
		return Config::get("module_dir") . preg_replace ("/wireframe_/i", "", $filename) . "/" . $filename . ".php";
	}

	public static function loadModuleModel ($filename)
	{
		return Config::get("module_dir") . preg_replace ("/wireframe_|_model/i", "", $filename) . "/" . $filename . ".php";
	}

	public static function loadModuleAjax($file_name)
	{
		return Config::get("module_dir") . preg_replace("/_ajax/i", "", $file_name) . "/" . $file_name . ".php";
	}

	public static function loadLib ($filename)
	{
		return "core/lib/" . $filename . ".php";
	}

}

spl_autoload_register ("Autoload::load");
Autoload::register ("Autoload::loadLib");
Autoload::register ("Autoload::loadModuleModel");