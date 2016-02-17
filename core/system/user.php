<?php

class User implements ArrayAccess {

	protected $name		= 'user';
	protected $model	= null;
	protected $data		= null;

	public function __construct($type = null)
	{
		$config = Config::getInstance();

		// user model
		$this->model = new UserModel;

		if (!empty (Params::$_SESSION[$this->name]) && is_array (Params::$_SESSION[$this->name]))
		{
			$this->data = Params::$_SESSION[$this->name];

		} elseif (!empty (Params::$_COOKIE[$this->name]))

			$uc = Params::$_COOKIE[$this->name];
			if (!empty ($uc['id']) && !empty ($uc['p'])) {
			$utmp = $this->model->getUserById ($uc['id'], $uc['p']);
			if (!empty ($utmp)) {
				$this->data = $utmp;
			}
		}

	}

	public function __destruct()
	{
		Params::$_SESSION[$this->name] = $this->data;
		session_write_close();
	}

	public function is()
	{
		return (bool)isset ($this->data['id']);
	}

	public function login ($data, $remember = false)
	{
		$this->data = $data;

		if ($remember) {
			Params::$_COOKIE->set ($this->name, array ('id' => $this->data['id'], 'p' => $this->data['password']), Config::get ('cookie_expire'));
		}
	}

	public function logout()
	{
		$this->data = null;
		Params::$_COOKIE->set ($this->name, null, -1);
	}

	public function update()
	{
		if ($this->is ()) {
			$data = $this->model->getUserById ($this->data['id']);
			$remember = (bool)Params::$_COOKIE[$this->name];
			$this->login ($data, $remember);
		}
	}

	public function getData()
	{
		return $this->data;
	}

	public function __get($var)
    {
    	if (!empty($this->data[$var])) {
    		return $this->data[$var];
    	} else {
    		return null;
    	}
    }

	public function __set($var, $value)
    {
    	$this->data[$var] = $value;
    }

    public function remove($var)
	{
		unset ($this->data[$var]);
	}

	/* ArrayAccess Interface */

	public function offsetExists($offset)
	{
		return !empty($this->data[$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}

}

class UserModel extends ProjectModel {

	function getUserById ($id, $passwd = null)
	{
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');

		$sql = "
			SELECT
				*
			FROM
				{$db_prefix}user
			WHERE
				id = ?";

		if (!empty ($passwd)) {
			$sql.= "
				AND
				password = '{$passwd}'";
		}

		return $this->db->prepare ($sql, array ($id))->getRow ();
	}

}
