<?php

class Page {

	// pages key map
	protected $id_key		 		= 'id';
	protected $parent_id_key 		= 'page_id';
	protected $url_key 		 		= 'url_title';

	// navigator key map
	protected $nav_id_key 	 		= 'nav_id';
	protected $nav_parent_id_key 	= 'nav_parent_id';
	protected $nav_item_id_key 	 	= 'item_id';
	protected $nav_url_key 		 	= 'url';
	protected $nav_item_type_key 	= 'type';
	protected $nav_default_type 	= 'item';
	protected $nav_module			= 'module';

	protected $default_page_flag	= 'default';

	// lang
	protected $accepted_lang		= array();
	protected $current_lang			= '';
	protected $url_include_lang		= true;

	// url settings
	protected $url_separator		= '/';
	protected $current_url			= array();
	protected $current_url_string	= '';
	protected $resolved_url_depth	= 0;

	// pages
	protected $pages		 		= array();
	protected $parent_pages 		= array();
	protected $current_page	 		= null;
	protected $url_map				= array();

	public static $url_space			= '_';

	public function __construct($languages = null)
	{
		$this->setLanguages($languages);

		// do not include language in url for single language project
		if (sizeof($languages) < 2 && !Config::get('force_url_lang')) {
			$this->setUrlIncludeLanguage(false);
		}
		$this->setCurrentUrl();
		if (Config::get ('url_space')) self::$url_space = Config::get ('url_space');
	}

	public function setLanguages($languages)
	{
		$this->accepted_lang = $languages;
	}

	public function setCurrentLang($lang)
	{
		$this->current_lang = $lang;
	}

	public function getCurrentLanguage()
	{
		return $this->current_lang;
	}

	public function setUrlIncludeLanguage($include = true)
	{
		$this->url_include_lang = $include;
	}

	protected function setCurrentUrl($url = null)
	{
		if (!$url) {
			$url = Params::$_SERVER['REQUEST_URI'];
		}
		$url = preg_replace ('/\-+/i', '-', $url);
		$url = preg_replace ('/\-$/i', '', $url);
		$url = preg_replace ('/^\-/i', '', $url);
		$url = explode('?', $url);
		foreach (explode($this->url_separator, $url[0]) as $key => $part) {
			if (strstr($part, '?')) {
				break;
			} else if (strlen($part)) {

				// set current lang
				if (!$this->current_url && in_array($part, $this->accepted_lang)) {
					$this->setCurrentLang($part);
				}
				$this->current_url[] = $part;
				$this->current_url_string.= $this->url_separator . $part;
			}
		}

		// set default lang
		if (!$this->current_lang && $this->accepted_lang) {
			$this->setCurrentLang($this->accepted_lang[0]);
		}
	}

	public function appendPage ($page)
	{
		$this->parent_pages[] = $page;
	}

	public function addPage($page)
	{
//		debug ($page);
		$page[$this->nav_id_key] = $page[$this->id_key];
		$page[$this->nav_parent_id_key] = $page[$this->parent_id_key];
		$this->pages[$page[$this->parent_id_key]][$page[$this->id_key]] = $page;
	}

	public function addPages($pages)
	{
		foreach ($pages as $page) {
			$this->addPage($page);
		}
	}

	public function getCurrentPage($default_page = null)
	{
		ksort($this->pages);
		$this->parent_pages = array();

		$parent_url = '';

		if ($this->accepted_lang && $this->url_include_lang) {
			$lang = $this->current_lang ? $this->current_lang : $this->accepted_lang[0];
			$parent_url = $this->url_separator . $this->urlEncode($lang);
		}
		if ($this->pages) {
			$this->buildNavigation(0, $parent_url);
		}

		// search url map
		$page = null;
		$url_map = '';

		foreach ($this->current_url as $depth => $url_part) {
			$url_map.= $this->url_separator . $url_part;
			if (!empty($this->url_map[$url_map])) {
				$page = $this->url_map[$url_map];
				$this->parent_pages[] = $page;
				$this->resolved_url_depth = $depth;
			} else if ($depth) {
				break;
			}
		}

		if ($page) {
			$this->current_page = $page;
		} else {
			if ($this->current_page = $this->getPageByParams($default_page)) {
				$this->current_page[$this->default_page_flag] = true;
				$this->parent_pages[] = $this->current_page;
				$this->setCurrentUrl($this->current_page[$this->nav_url_key]);
			}
		}

		return $this->current_page;
	}

	public function addItem($item, $parent_page = null, $type = null, $module = null)
	{
		$type = $type ? $type : $this->nav_default_type;

		// get parent page
		if (is_numeric ($parent_page)) {
			$parent_id = $parent_page;
		} elseif (!$parent_page) {
			$parent_page = $this->current_page;
			$parent_id = $parent_page[$this->nav_id_key];
		} else {
			$parent_id = $parent_page[$this->nav_id_key];
		}

		if ($parent_id === 0 && $this->accepted_lang && $this->url_include_lang) {
			$lang = $this->current_lang ? $this->current_lang : $this->accepted_lang[0];
			$parent_url = $this->url_separator . $this->urlEncode($lang);
		} else {
			$parent_url = '';
		}

		// create page
		$page = $item;
		$page[$this->nav_id_key] = $type . ($module != null ? '_' . $module : '') . $item[$this->id_key];
		$page[$this->nav_parent_id_key] = $parent_id;
		$page[$this->nav_item_id_key] = $item[$this->id_key];
		$page[$this->nav_item_type_key] = $type;
//		$page[$this->nav_module] = $module;

		// set url
		$page_url = $this->urlConvert($page[$this->url_key]);
		$page[$this->nav_url_key] = $parent_url . $parent_page[$this->nav_url_key] . $this->url_separator . $page_url;

		// add to pages
		$this->pages[$parent_id][$page[$this->nav_id_key]] = $page;

		// add to url map
		$this->url_map[$page[$this->nav_url_key]] = $page;

		// save to items map
		$this->items_id_map[$page[$this->nav_id_key]] = $page;

		return $page;
	}

	public function getCurrentItem($type = null, $module = null)
	{
		$type = $type ? $type : $this->nav_default_type;
		if (!empty ($module)) {
			$type.= '_' . $module;
		}
		$current_item = null;
		for ($i = $this->resolved_url_depth; $i < sizeof($this->current_url); $i++) {
			$url_map = $this->url_separator . implode($this->url_separator, array_slice($this->current_url, 0, $i + 1));
			if (!empty($this->url_map[$url_map])) {
				$item = $this->url_map[$url_map];

				// set parent
				$this->parent_pages[$this->url_include_lang ? $i - 1 : $i] = $item;

				// check for item type
				if (isset($item[$this->nav_item_id_key]) && ($item[$this->nav_item_type_key] == $type)) {// && $item[$this->nav_module] ==  $module) {

					// set resolved depth
					if ($this->resolved_url_depth < $i) {
						$this->resolved_url_depth = $i;
					}
					$current_item = $this->items_id_map[$item[$this->nav_id_key]];
				}
			}
		}
		return $current_item;
	}

	protected function buildNavigation($parent_id = 0, $parent_url = '')
	{
		foreach ($this->pages[$parent_id] as &$page) {
			$page_url = $this->urlConvert($page[$this->url_key]);
			$page[$this->nav_url_key] = $parent_url . $this->url_separator . $page_url;
			$this->url_map[$page[$this->nav_url_key]] = $page;
			$this->pages_id_map[$page[$this->nav_id_key]] = $page;
			$parent_id = $page[$this->nav_id_key];
			if ($parent_id && !empty($this->pages[$parent_id])) {
				$this->buildNavigation($parent_id, $page[$this->nav_url_key]);
			}
		}
	}

	public function getNavigation($search_params = null, $group_by = 'type')
	{
		if ($search_params) {
			$pages = $this->search($search_params, false);
		} else {
			$pages = $this->pages;
		}
		if ($group_by) {
			$group_pages = array();
			foreach ($pages as $parent_id => $parent_pages) {
				Utils::sortBySubkey ($parent_pages, 'ord', SORT_ASC);	//	Sorts by ORD items and menu items
				foreach	($parent_pages as $page) {
					$group_pages[$page[$group_by]][$parent_id][] = $page;
				}
			}
			return $group_pages;
		} else {
			return $pages;
		}
	}

	public function getParentPages()
	{
		return $this->parent_pages;
	}

	public function getChilds($parent_page = null)
	{
		if (!$parent_page) {
			$parent_page = $this->current_page;
		}
		return $this->getAllChilds($parent_page[$this->nav_id_key]);
	}

	public function getChildsList($parent_page = null)
	{
		$list = array();
		$childs = $this->getChilds($parent_page);
		foreach ($childs as $parent_id => $items) {
			foreach ($items as $item) {
				$list[] = $item;
			}
		}
		return $list;
	}

	private function getAllChilds($parent_id, $childs = array())
	{
		if (!empty($this->pages[$parent_id])) {
			$childs[$parent_id] = $this->pages[$parent_id];
			foreach ($childs[$parent_id] as $child) {
				$childs = $this->getAllChilds($child[$this->nav_id_key], $childs);
			}
		}
		return $childs;
	}

	public function getChildItems($parent_item)
	{
		$items = array();
		if ($parent_item) {
			if ($results = $this->search(array($this->nav_parent_id_key => $parent_item[$this->nav_id_key]))) {
				foreach ($results[$parent_item[$this->nav_id_key]] as $item) {
					$items[] = $this->getItemById($item[$this->nav_item_id_key], $item[$this->nav_item_type_key]);
				}
			}
		}
		return $items;
	}

	public function getPageById($id)
	{
		if (!empty($this->pages_id_map[$id])) {
			return $this->pages_id_map[$id];
		}
	}

	public function getItemById($id, $type = null, $module = null)
	{
		$type = $type ? $type : $this->nav_default_type;
		if (!empty ($module)) {
			$type.= '_' . $module;
		}

		if (!empty($this->items_id_map[$type . $id])) {
			return $this->items_id_map[$type . $id];
		}
	}

	public function getPageByParams($params)
	{
		return $this->search($params, true);
	}

	public function searchPages($params, $first_match = false, $group_by_parent = true)
	{
		return $this->search($params, $first_match, $group_by_parent);
	}

	private function search($params, $first_match = false, $group_by_parent = true)
	{
		if ($this->pages) {
			$result = $this->doSearch($params, $first_match, 0, array (), $group_by_parent);
			if ($first_match && $result) {
				list($parent_id, $result) = each($result);
				return $result[0];
			} elseif ($result) {
				return $result;
			}
		}
		return null;
	}

	private function doSearch($params, $first_match, $parent_id = 0, $results = array(), $group_by_parent = true)
	{
		foreach ($this->pages[$parent_id] as $page) {
			$match = true;
			if (is_array ($params)) {
				foreach ($params as $param => $value) {
					if (!isset($page[$param]) || ($page[$param] != $value)) {
						$match = false;
						break;
					}
				}
						}
				if ($match) {
					if ($group_by_parent) {
						$results[$page[$this->nav_parent_id_key]][] = $page;
					} else {
						$results[] = $page;
					}

				}
				if (!($results && $first_match)) {
					$parent_id = $page[$this->nav_id_key];
					if (!empty($this->pages[$parent_id])) {
						$results = $this->doSearch($params, $first_match, $parent_id, $results, $group_by_parent);
					}
				}
		}
		return $results;
	}

	public static function urlConvert($string)
	{
		$string = trim ($string);
		$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		$string = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $string);
		$string = strtolower(trim($string, '-'));
		$string = preg_replace('/[\/_|+ -]+/', '-', $string);

		return $string;
	}

	public static function urlEncode($string, $param = '')
	{
		if (is_array($string)) {
			return self::array2Url($string, $param);
		}
		return urlencode($string);
	}

	public static function urlDecode($string)
	{
		return urldecode($string);
	}

	public static function array2Url($array, $param)
	{
		$string = '';
		foreach ($array as $key => $value) {
			$string.= $string ? '&amp;' : '';
			$string.= $param . '[' . urlencode($key) . ']=' . urlencode($value);
		}
		return $string;
	}

	public function getParam($index = 0)
	{
		if (is_int($index)) {
			$index += 1;
			if ($index <= 0) {
				$index = sizeof($this->current_url) + $index;
				if (($index + 1) >= $this->resolved_url_depth) {
					return $this->getUrlIndex($index);
				}
			} else {
				return $this->getUrlIndex($this->resolved_url_depth + $index);
			}
		} else {
			return Params::$_GET[$index];
		}
	}

	public function getUrlIndex($index = 0)
	{
		if (!empty($this->current_url[$index])) {
			return $this->urlDecode($this->current_url[$index]);
		}
	}

	function getUrl($params = array(), $page = null, $includeOld = true)
	{
		// separate url/get params
		$add_params = array();
		$get_params = array();
		foreach ($params as $param => $value) {
			if (is_int($param)) {
				$add_params[$param] = $value;
			} else {
				$get_params[$param] = $value;
			}
		}

		// add url params
		if ($add_params) {
			ksort($add_params);
			$url = $this->addParams($add_params, $page);

		// get page url
		} else if ($page) {
			$url = $page[$this->nav_url_key];

		// current url
		} else {
			$url = $this->current_url_string;
		}
		$_GET_params = array();
		if ($includeOld) {
			if (is_array($includeOld)) {
				foreach ($includeOld as $param) {
					if ($value = Params::$_GET[$param]) {
						$_GET_params[$param] = $value;
					}
				}
			} else {
				$_GET_params = Params::$_GET->get();
			}
		}
		$params = array_merge($_GET_params, $get_params);
		if ($params) {
			$url.= '?';
			$cnt = 0;
			foreach ($params as $name => $value) {
				if (!is_null($value)) {
					$url.= $cnt ? '&amp;' : '';
					if (is_array($value)) {
						$url.= $this->urlEncode($value, $name);
					} else {
						$url.= $name . '=' . $this->urlEncode($value);
					}
					$cnt++;
				}
			}
			if (!$cnt) {
				$url = substr($url, 0, -1);
			}
		}
		return $url;
	}

	public function addParams ($params, $page = null)
	{
		if (!$page) {
			$url = '';
			for ($i = 0; $i <= $this->resolved_url_depth; $i++) {
				$url.= $this->url_separator . $this->current_url[$i];
			}

		} else {
			$url = $page[$this->nav_url_key];
		}
		foreach ($params as $value) {
			if (strlen($value)) {
				$url.= $this->url_separator . $this->urlConvert($value);
			}
		}
		return $url;
	}

	public function redirect ($url = null, $params = array(), $page = null, $includeOld = true)
	{
		if (!$url) {
			$url = $this->getUrl($params, $page, $includeOld);
		}
		if (!$url) {
			$url = $this->url_separator;
		}
		header('Location: '. str_replace('&amp;', '&', $url));
		exit;
	}

	public function cachePagesSet ()
	{
		Cache::set ('sys_page_class', array ('pages' => $this->pages, 'url_map' => $this->url_map, 'items_id_map' => $this->items_id_map), true, Config::get ('memcache_timeout'));
	}

	public function cachePagesGet ()
	{
		if ($array = Cache::get ('sys_page_class')) {
			$this->pages = $array['pages'];
			$this->url_map = $array['url_map'];
			$this->items_id_map = $array['items_id_map'];

			return true;
		}
		return false;

	}
}
