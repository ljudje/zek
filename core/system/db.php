<?php

class Db {

	public static $db	= null;
	private $dbObj		= null;
	private $connection = null;
	private $query		= null;
	private $result 	= null;
	public static $cnt	= 0;
	public $useMySqli	= true;
	public $debug		= false;

	public function __construct()
	{
		if (!function_exists ('mysqli_query')) $this->useMySqli = false;
	}

	public static function create ($connection_string, $force = false) {
		if ($force) {
			$db = new Db ();
			$db->connect ($connection_string);
			return $db;
		} elseif (!self::$db) {
			self::$db = new Db ();
			self::$db->connect ($connection_string);
		}
		return self::$db;
	}

	public function connect ($connection_string) {
		if (preg_match('~^[a-z]+://([^:/@]*):([^/@]*)@([^:/@]*):?(\d*)?/([^:/@]*)$~i', $connection_string, $matches)) {	//	OLD: ^[a-z]+://([^:/@]*):([^:/@]*)@([^:/@]*)/([^:/@]*)$
			list(, $user, $pass, $host, $port, $name) = $matches;
			if (empty ($port)) $port = 3306;
			if ($this->useMySqli) {
				if ($this->connection = @mysqli_connect ($host, $user, $pass, $name, $port)) {
					mysqli_select_db ($this->connection, $name);
				} else {
					die ("Error connecting to DB.");
					//throw new DatabaseException('Can not connect to database.');
					//	TODO: exception
				}
			} else {
				if ($this->connection = @mysql_connect ($host . ':' . $port, $user, $pass)) {
					mysql_select_db ($name, $this->connection);
				} else {
					die ("Error connecting to DB.");
					//throw new DatabaseException('Can not connect to database.');
					//	TODO: exception
				}
			}
		}
	}

	public function setCharset ($charset) {
		if ($this->useMySqli) {
			mysqli_query ($this->connection, "SET NAMES " . $charset);
		} else {
			mysql_query ("SET NAMES " . $charset, $this->connection);
		}
	}

	public function setTimezone ($timezone) {
		if ($this->useMySqli) {
			mysqli_query ($this->connection, "SET time_zone = '" . $timezone . "'");
		} else {
			mysql_query ("SET time_zone = '" . $timezone . "'", $this->connection);
		}
	}

	public function prepare ($query, $vars = array ()) {
		if (!empty ($vars)) {
			if (is_int (key ($vars))) {
				$query = preg_replace ("/\?/", '__?__', $query);
			}
			foreach ($vars as $key => $item) {
				if (!is_null ($item)) $item = trim ($item);
				if (is_int ($item)) {
					$item = (int)$item;

				} elseif (is_null ($item)) {
					$item = "NULL";

				} else {
					$item = "'" . $this->dbEscape ((string)$item) . "'";

				}
//				$item = is_int ($item) ? (int)$item : "'" . $this->dbEscape ((string)$item) . "'";

				if (!is_int ($key)) {
					$query = preg_replace ("/(:" . $key . ")(?=(?:[^\"']|[\"|'][^\"']*[\"|'])*$)/si", $item, $query, 1);
				} else {
					$query = preg_replace ("/(__\?__)(?=(?:[^\"']|[\"|'][^\"']*[\"|'])*$)/si", $item, $query, 1);
				}
			}
		}
		//	TODO: tole mora bit bolj bulletproof, 100%!
		$this->query = $query;
		return $this;
	}

	public function getAll ($group = null) {
		$array = array ();
		$this->run();
//		$this->result = mysqli_query ($this->connection, $this->query);
		while ($row = call_user_func ($this->useMySqli ? 'mysqli_fetch_assoc' : 'mysql_fetch_assoc', $this->result)) {
			if (is_array ($group)) {
				if (sizeof ($group) == 1) {
					if (!empty ($row[$group[0]]) || $row[$group[0]] === '0') {
						$array[$row[$group[0]]][] = $row;
					}
				} else {
					if (!empty ($row[$group[0]]) || $row[$group[0]] === '0') {
						$array[$row[$group[0]]] = $row[$group[1]];
					}
				}
			} elseif (is_string ($group)) {
				if (!empty ($row[$group]) || $row[$group] === '0') {
					$array[$row[$group]] = $row;
				}
			} else {
				$array[] = $row;
			}
		}
		return $array;
	}

	public function getRow ($field = null) {
		$array = array ();
		$this->run();
//		$this->result = mysqli_query ($this->connection, $this->query);
		$array = call_user_func ($this->useMySqli ? 'mysqli_fetch_assoc' : 'mysql_fetch_assoc', $this->result);

		if (!empty ($field)) {
			return $array[$field];
		}
		return $array;
	}

	public function getSingleRow ($field = null) {
		$array = array ();
//		$this->result = mysqli_query ($this->connection, $this->query);
		$array = call_user_func ($this->useMySqli ? 'mysqli_fetch_assoc' : 'mysql_fetch_assoc', $this->result);

		if (!empty ($field)) {
			return $array[$field];
		}
		return $array;
	}

	public function run () {
		if ($this->debug) {
	 		$start = microtime (true);
		}

		if ($this->useMySqli) {
			$this->result = mysqli_query ($this->connection, $this->query);
		} else {
			$this->result = mysql_query ($this->query, $this->connection);
		}

		if ($this->debug) {
			print_r ($this->query);
			echo "\n";
			print_r (microtime (true) - $start);
			if (Params::$_GET['debug'] != 'all')
				$this->debug = false;
		}

		if (call_user_func ($this->useMySqli ? 'mysqli_errno' : 'mysql_errno', $this->connection)) {
			//	TODO: exception
			if (ini_get ('display_errors')) {
				debug (call_user_func ($this->useMySqli ? 'mysqli_error' : 'mysql_error', $this->connection));
				print_r ('<pre>' . $this->query . '</pre>');
				exit();
			} else {
				exit();
			}
		} else {
//			debug ($this->query);
			self::$cnt++;
		}
	}

	public function dbEscape ($string)
	{
		if ($this->useMySqli) {
			return mysqli_real_escape_string ($this->connection, $string);
		} else {
			return mysql_real_escape_string ($string, $this->connection);
		}
	}

	public function getLastId () {
		return call_user_func ($this->useMySqli ? 'mysqli_insert_id' : 'mysql_insert_id', $this->connection);
	}

	public function getAffectedRows () {
		return call_user_func ($this->useMySqli ? 'mysqli_affected_rows' : 'mysql_affected_rows', $this->connection);
	}

	public function __destruct ()
	{
		if ($this->connection) {
			call_user_func ($this->useMySqli ? 'mysqli_close' : 'mysql_close', $this->connection);
		}
	}

}
