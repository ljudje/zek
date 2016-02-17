<?php

class KLocale implements ArrayAccess {

	protected $data = array ();

	public function __construct()
	{
		$this->model = new KLocaleModel;
		$this->data = $this->model->getLocale ();
	}

	public function set($name, $data)
	{
		$this->data[$name] = $data;
	}

	public function get($name = null)
	{
		if (!$name) {
			return $this->data;
		} else if ($this->is($name)) {
			return $this->data[$name];
		} else {
			return null;
		}
	}

	public function is($name)
	{
		return isset($this->data[$name]);
	}

	public function remove($name)
	{
		if ($this->is($name)) {
			unset($this->data[$name]);
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

class KLocaleModel extends Model {

	public function __construct()
	{
		$this->db		= Config::get("db");
		$this->config	= Config::getInstance();
		$this->lang		= Config::get("lang");
		$this->pages	= Config::get("page");
	}

	public function getLocale ()
	{
		if ($array = Cache::get ('locale')) {
			return $array;
		} else {
			$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
			$array = array ();
			$sql = "
				SELECT
					{$db_prefix}locale.code,
					{$db_prefix}locale.module_id,
					CASE WHEN {t:{$db_prefix}locale}.content IS NULL THEN {$db_prefix}locale.content ELSE {t:{$db_prefix}locale}.content END as content
				FROM
					{$db_prefix}locale
				{j:{$db_prefix}locale}
				ORDER BY
					{$db_prefix}locale.id ASC
			";

			$tmp = $this->translate ($sql)->getAll ();
			foreach ($tmp as $item) {
				$array[$item['code']] = $item['content'];
			}
			Cache::set ('locale', $array, true, Config::get ('memcache_timeout'));
		}
		return $array;
	}

}
