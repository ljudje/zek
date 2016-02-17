<?php

class Project {

	protected $config		= null;
	protected $error_handler= null;
	protected $lang			= null;
	protected $page			= null;
	protected $user			= null;
	protected $model		= null;
	protected $wireframe	= null;
	protected $errors		= null;

	public function __construct()
	{
		// project config
		$this->config = Config::get('config');

		// error handler
//		$this->error_handler = Config::get('error_handler');

		// init params
		$this->setParams();

		try {
			// connect to database
			$this->setDB();

			// setup languages
			$this->setLanguage();
			Config::set("lang", $this->lang);

			// create navigator
			$this->setPage();
			Config::set("page", $this->page);

			// create user
			$this->setUser();
			Config::set("user", $this->user);

			// Autoload register
			Autoload::register('Autoload::loadWireframeModel');
			Autoload::register('Autoload::loadWireframe');
			Autoload::register('Autoload::loadModuleModel');
			Autoload::register('Autoload::loadModule');

			// create model
			$this->setModel();

		} catch (DatabaseException $e) {
			$this->errors[] = $e;
		}
	}

	public function build ()
	{
		if (!$this->errors) {

//			try {
				// load pages from db
				$this->page->addPages($this->model->getPages());

				// get current page
				$page = $this->getCurrentPage();

				if (!empty($page['default']) && !Config::get ('ignore_home')) {
					$request_uri = Params::$_SERVER['REQUEST_URI'];
					if ($pos = strpos($request_uri, '?')) {
						$request_uri = substr($request_uri, 0, $pos);
					}
					$request_uri = strtolower(str_replace('/', '', $request_uri));
					$lang_ids = $this->lang->getIds();

					if ($request_uri && !in_array($request_uri, $lang_ids)) {
						header ('Location: /');

//						header('HTTP/1.1 404 Not Found');
//						$url = 'http://' . Params::$_SERVER['HTTP_HOST'];
//						die('<h1>Not Found</h1> Go back to <a href="'. $url .'">'. $url .'</a>');
					}
				}

				// get page contents
				$contents = $this->model->getContents($page['id']);

				// create wireframe
				if (empty ($this->wireframe)) {
					$this->wireframe = Wireframe::create($page, $contents);
					$this->wireframe->build();
				}
				$output = $this->wireframe->fetch();

			// handle uncaught exception
//			} catch (Exception $e) {
//			}

		// handle project exceptions
		} else {
			foreach ($this->errors as $e) {
				$this->error_handler->handleException($e);
			}
		}

		// display errors
//		if ($this->config['php_display_errors']) {
//			$output.= $this->error_handler->displayErrors();
//		}
		return $output;
	}

	protected function setParams()
	{
		Params::init();
	}

	protected function setDB()
	{
		$db = Db::create($this->config['db_dsn']);
		$db->setCharset($this->config['db_charset']);
		if (!empty ($this->config['db_timezone'])) $db->setTimezone ($this->config['db_timezone']);
		Config::set("db", $db);
	}

	protected function setLanguage()
	{
		$this->lang = new Language;
	}

	protected function setPage()
	{
		$this->page = new Page($this->lang->getIds());
		$this->lang->setCurrent($this->page->getCurrentLanguage());
		$this->lang->setLocaleLang($this->page->getCurrentLanguage());
		$current = $this->page->getCurrentLanguage();
		Config::set ('lang_current', $current);
	}

	protected function setUser()
	{
		$this->user = new User;
	}

	protected function setModel()
	{
		$this->model = new ProjectModel;
	}

	protected function getCurrentPage()
	{
		return $this->page->getCurrentPage ($this->config['default_page']);
	}
}
