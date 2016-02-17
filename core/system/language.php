<?php

class Language implements ArrayAccess, Iterator, Countable {

	protected $languages		= array();
	protected $current_lang		= '';
	protected $current_lang_arr	= array();
	protected $is_default		= false;
	protected $current_loc		= '';

	public function __construct()
	{
		$this->languages = $this->getLanguages();

		// set default
		foreach ($this->languages as $lang) {
			if ($lang['is_default']) {
				$this->is_default = true;
				break;
			}
		}
	}

	public function setLocaleLang ($lng)
	{
		$lang = $this->languages[$lng];

		$locale = (Config::get('php_locale') != null) ? Config::get('php_locale') : $lang['locale'];

		$this->current_loc = $locale;
		$this->current_lang = $lang['id'];
//		$this->is_default = true;
		setlocale (LC_ALL, $locale);

		setlocale (LC_NUMERIC, 'en_US'); //	Fix for fuckin' decimal commas on servers where admins set up Slovenian/Non-US OS
	}

	private function getLanguages () {
		if ($array = Cache::get ('sys_languages')) {
			return $array;
		} else {
			$db = Config::get("db");
			$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
			$sql = "
				SELECT
					{$db_prefix}language.*
				FROM
					{$db_prefix}language
				WHERE
					NOT hidden
				ORDER BY
					is_default DESC,
					ord ASC,
					id ASC";

			$tmp = $db->prepare ($sql, array ())->getAll();
			foreach ($tmp as $item) {
				$array[$item['id']] = $item;
			}

			Cache::set ('sys_languages', $array, true, Config::get ('memcache_timeout'));
		}
		return $array;
	}

	public function getIds()
	{
		return array_keys($this->languages);
	}

	public function setCurrent($lang)
	{
		$this->current_lang = $lang;
		$this->is_default = $this->languages[$lang]['is_default'];
		$this->current_lang_arr = $this->languages[$lang];
	}

	public function getCurrent()
	{
		return $this->current_lang;
	}

	public function getCurrentArr()
	{
		return $this->current_lang_arr;
	}

	public function isDefault()
	{
		return $this->is_default;
	}

	public function getLocale()
	{
		return $this->current_loc;
	}

	/* Iterator Interface */

	public function current()
	{
		return current($this->languages);
	}

	public function next()
	{
		return next($this->languages);
	}

	public function rewind()
	{
		return reset($this->languages);
	}

	public function key()
	{
		return key($this->languages);
	}

	public function valid()
	{
		return !is_null($this->key());
	}

	/* Countable Interface */

	public function count()
	{
		return count($this->languages);
	}

	/* ArrayAccess Interface */

	public function offsetExists($offset)
	{
		return isset($this->languages[$this->current_lang][$offset]);
	}

	public function offsetGet($offset)
	{
		return $this->languages[$this->current_lang][$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->languages[$this->current_lang][$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->languages[$this->current_lang][$offset]);
	}

}
