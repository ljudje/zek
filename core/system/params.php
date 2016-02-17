<?php

class ParamsStore implements ArrayAccess {

	/**
	 * Params data store
	 *
	 * @var array
	 */
	protected $store = array();

	/**
	 * Params constructor
	 *
	 * Sets the data store
	 *
	 * @param mixed $data
	 */
	public function __construct(&$data)
	{
		$this->store = &$data;
	}

	/**
	 * Set params data
	 *
	 * @param string $name
	 * @param mixed $data
	 */
	public function set($name, $data)
	{
		$this->store[$name] = $data;
	}

	/**
	 * Get params data
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get($name = null, $default = null)
	{
		if (!$name) {
			return $this->store;
		} else if ($this->is($name)) {
			return $this->store[$name];
		} else {
			return $default;
		}
	}

	/**
	 * Check if params data exists
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function is($name)
	{
		return isset($this->store[$name]);
	}

	/**
	 * Remove data
	 *
	 * @param string $name
	 */
	public function remove($name)
	{
		if ($this->is($name)) {
			unset($this->store[$name]);
		}
	}

	public function offsetExists($offset)
	{
		return $this->is($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}
}

class Params {

	public static $_GET;
	public static $_POST;
	public static $_FILES;
	public static $_SERVER;
	public static $_SESSION;
	public static $_COOKIE;
	public static $_FLASH;

	public static function init()
	{
		self::$_GET 	= new _GET();
		self::$_POST 	= new _POST();
		self::$_FILES 	= new _FILES();
		self::$_SERVER 	= new _SERVER();
		self::$_SESSION = new _SESSION();
		self::$_COOKIE 	= new _COOKIE();
	}
}

class _GET extends ParamsStore {

	public function __construct()
	{
		$this->store = &$_GET;
	}

}

class _POST extends ParamsStore {

	public function __construct()
	{
		$this->store = &$_POST;
	}

}

class _FILES extends ParamsStore {

	public function __construct()
	{
		$this->store = &$_FILES;
	}

}

class _SERVER extends ParamsStore {

	public function __construct()
	{
		$this->store = &$_SERVER;
	}

}

class _SESSION extends ParamsStore {

	public function __construct()
	{
		$this->store = &$_SESSION;
	}

}

class _COOKIE extends ParamsStore {

	public function __construct()
	{
		$this->store = &$_COOKIE;
	}

	public function set($name, $data, $expiration = 15768000, $path = '/')
	{
		$this->store[$name] = serialize ($data);
		$secure = (Config::get ('cookie_secure') != '') ? (bool)Config::get ('cookie_secure') : false;
		setcookie ($name, $this->store[$name], ($expiration <= 0 ? $expiration : time() + $expiration), $path, Config::get ('cookie_domain'), $secure, true); // false, true ?
	}

	public function get($name = null, $default = null)
	{
		if (!$name) {
			return $this->store;
		} else if ($this->is($name)) {
			return unserialize ($this->store[$name]);
		} else {
			return $default;
		}
	}

}
