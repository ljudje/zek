<?php

class Module extends Wireframe {

	protected $content		= array();
	protected $page			= array();
	protected $pages		= array();
	protected $lang			= array();
	protected $mode			= '';
	protected $model		= null;
	protected $wireframe	= null;
	protected $skip			= false;
	protected $xclass		= '';
	protected $template		= null;

	public function __construct ($page, $content)
	{
		// set content
		$this->page = $page;
		$this->content = $content;
		$this->module = $content['module'];

		// create model
		$model = $content['module'] . 'Model';
		$this->model = new $model;

		$this->tpl = new Template;
		$this->pages = Config::get('page');
		$this->lang = Config::get('lang');
		$this->assign('lang', $this->lang);

		// user
		$this->user = Config::get('user');
		$this->assign ('user', $this->user);

		// dictionary
		$this->locale = Config::get('locale');
		$this->assign('locale', $this->locale);

		$this->assign('page', $this->page);
		$this->assign('content', $this->content);
		$this->assign('domain', 'http://' . Params::$_SERVER['SERVER_NAME']);
		$this->assign('site_id', Config::get ('site_id'));

		// set mode
		if ($this->content['mode'] && ($this->content['mode'] != $this->content['module'])) {
			$this->mode = $this->content['mode'];
		} else {
			$this->mode = $this->content['module'];
		}
		$this->setTemplate ($this->mode . ".tpl");

		$image = Image::getInstance();
		$this->assign('imageCache', $image);
	}

	protected function run($method)
	{
		return $this->$method();
	}

	public function build()
	{
		$output = $this->run('build' . $this->mode);

		// add main plugin css
		if ($this->mode != $this->content['module']) {
			$this->addCSS($this->content['module'] . '.css', $this->mode);
		}

		// add css and js
		$this->addCSS ($this->mode . '.css');
		$this->addJS ($this->module . '.js');
		$this->addJS ($this->mode . '.js');

		return $output;
	}

	final public function buildAjax()
	{
		$method = $this->getMethod();
		return $this->$method();
	}

	public function setTemplate ($filename, $cacheTime = null)
	{
		$class = strtolower(preg_replace('/([a-z0-9])([A-Z])/', '\\1_\\2', $this->module));
		$this->template = $filename;

		$cache_id = (!empty ($this->content['id'])) ? $this->content['id'] . '_' . $this->content['module_id'] : $this->content['module_id'] . '_' . $this->content['mode'];
		$cache_id.= $filename . md5 (Params::$_SERVER['REQUEST_URI']);

		$this->tpl->setTemplate ($this->template, false, Config::get ("module_dir") . $class . "/templates/", $cacheTime, $cache_id);
	}

	public function setWireframe($wireframe)
	{
		$this->wireframe = $wireframe;
	}

	public function addCSS ($css, $module = null, $media = 'screen') {
		$this->wireframe->addCSS ($css, $this->module);
	}

	public function addJS ($js, $module = null) {
		$this->wireframe->addJS ($js, $this->module);
	}

	final protected function getMethod()
	{
		$method = 'build'. $this->mode;
		if (method_exists($this, $method)) {
			return $method;
		} else {
			//throw new FrameworkException("Plugin ". get_class($this) ." must implement public method ". $method ."()");
		}
	}

	protected function skip ($skip = true)
	{
		$this->skip = $skip;
	}

	protected function addClass ($class)
	{
		$this->xclass = $class;
	}

}
