<?php

class Model {

	protected $db			= null;
	protected $page			= null;
	protected $config		= null;
	protected $locale		= null;
	protected $user			= null;
	protected $lang			= null;

	public function __construct()
	{
		$this->db		= Config::get("db");
		$this->pages	= Config::get("page");
		$this->config 	= Config::getInstance();
		$this->locale	= Config::get("locale");
		$this->lang		= Config::get("lang");
		$this->user		= Config::get("user");
	}

	protected function translate ($sql, $vars = null, $force_default_language = false, $force_translation = false)
	{
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');

		if (($this->lang->isDefault () || $force_default_language) && !$force_translation) {
			$sql = preg_replace ("/(\{t:(.+?)\})/i", '$2', $sql);
			$sql = preg_replace ("/(\{(j|cj|rj):(.+?)\})/i", "", $sql);	//	before: $3

		} else {
			$tr_postfix = Config::get("translation_table");
			$patterns[] = "/(\{t:(" . $db_prefix . ")?(.+?)\})/i";
			$patterns[] = "/(\{cj:(" . $db_prefix . ")?(.+?)\})/i";
			$patterns[] = "/(\{rj:(" . $db_prefix . ")?(.+?)\})/i";
			$patterns[] = "/(\{j:(" . $db_prefix . ")?(.+?)\})/i";

			$replacement[] = "$2" . "$3" . $tr_postfix;
			$replacement[] = "CROSS JOIN " . "$2" . "$3" . $tr_postfix . " ON " . "$2" . "$3" . $tr_postfix . "." . "$3" . "_id = " . "$2" . "$3" . ".id AND " . "$2" . "$3" . $tr_postfix . ".language_id = '" . $this->pages->getCurrentLanguage() . "'";
			$replacement[] = "RIGHT JOIN " . "$2" . "$3" . $tr_postfix . " ON " . "$2" . "$3" . $tr_postfix . "." . "$3" . "_id = " . "$2" . "$3" . ".id AND " . "$2" . "$3" . $tr_postfix . ".language_id = '" . $this->pages->getCurrentLanguage() . "'";
			$replacement[] = "LEFT JOIN " . "$2" . "$3" . $tr_postfix . " ON " . "$2" . "$3" . $tr_postfix . "." . "$3" . "_id = " . "$2" . "$3" . ".id AND " . "$2" . "$3" . $tr_postfix . ".language_id = '" . $this->pages->getCurrentLanguage() . "'";

			$sql = preg_replace ($patterns, $replacement, $sql);	//	translate joins
		}

		if (!empty ($vars)) {
			return $this->db->prepare ($sql, $vars);
		} else {
			return $this->db->prepare ($sql);
		}
	}

	protected function limit ($sql, $vars = null, $limit = 10, $calcrows = true, $pagevar = 'page', $force_default_language = false, $force_translation = false)
	{
		$array = array ();
		$array['page'] = Params::$_GET->get($pagevar, 1);
		$array['pagevar'] = $pagevar;
		$array['limit'] = $limit;
		$array['limit_sql'] = $limit + 1;
		$array['limit_start'] = $limit * ($array['page'] - 1);

		if ($calcrows) {
			$sql = preg_replace ("/SELECT/", "SELECT SQL_CALC_FOUND_ROWS", $sql, 1);
			$sql.= " LIMIT " . $array['limit_start'] . "," . $array['limit'];
		} else {
			$sql.= " LIMIT " . $array['limit_start'] . "," . $array['limit_sql'];
		}

		$items = $this->translate ($sql, $vars, $force_default_language, $force_translation)->getAll ();
		$array['results'] = sizeof ($items);

		if ($calcrows) {
			$array['total_rows'] = $this->db->prepare ("SELECT FOUND_ROWS() AS rows")->getRow ("rows");
			$array['pages'] = ceil ($array['total_rows'] / $limit);

		} else {
			if ($array['results'] > $limit) {
				$array['next'] = true;
				$array['results'] = $limit;
				$items = array_slice ($items, 0, -1);
			}
			$array['pages'] = $array['page'];
		}
		$array['paging'] = (!empty ($array['next']) || $array['pages'] > 1);

		$result['data'] = $array;
		$result['items'] = $items;

		return $result;
	}

}
