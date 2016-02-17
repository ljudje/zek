<?php

class Wireframe {

	protected $tpl				= null;
	protected $module_template	= null;
	protected $page				= null;

	public $page_title			= array ();
	public $page_keywords		= array ();
	public $page_description	= array ();

	protected $css	= array ();
	protected $js	= array ();

	public static function create ($page, $content)
	{
		if (self::isAjaxRequest()) {
			//	TODO
			Autoload::register('Autoload::loadModuleAjax');

			// create wireframe
			$wireframe = new WireframeAjax ($page, $content);
		} else {
			$wireframe_class = 'Wireframe'. ucfirst ($page['wireframe']);
			$wireframe = new $wireframe_class($page, $content);
		}
		return $wireframe;
	}

	public static function isAjaxRequest () {
		return ((Params::$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && (Params::$_GET['module'] || Params::$_POST['module']) || isset ($_GET['ajaxdebug']));
	}

	public function setup()
	{
		// create template
		$this->tpl = new Template;

		Config::set('tpl', $this->tpl);

		// project config
		$this->config = Config::getInstance();
		$this->assign('config', $this->config);

		// create dictionary
		$this->locale = new KLocale;
		Config::set('locale', $this->locale);
		$this->assign('locale', $this->locale);

		// user
		$this->user = Config::get('user');
		$this->assign('user', $this->user);

		// lang
		$this->lang = Config::get('lang');
		$this->assign('lang', $this->lang);

		$this->pages = Config::get("page");
		$this->assign('current_lang', $this->pages->getCurrentLanguage());
		$this->assign('current_lang_arr', $this->lang->getCurrentArr());

		// assign page
		$this->assign ('page', $this->page);

		// set wireframe template
		$this->setTemplate($this->page['wireframe'] .'.tpl');
//		Config::get("template_dir") .

		foreach (Config::get ("default_css") as $css) {
			$this->addCSS ($css);
		}

		foreach (Config::get ("default_js") as $js) {
			$this->addJS ($js);
		}

		$image = Image::getInstance();
		$this->assign('imageCache', $image);
	}

	public function assign ($var, $value = null)
	{
		$this->tpl->assign ($var, $value);
	}

	public function setTemplate ($filename, $cacheTime = null)
	{
		$this->tpl->setTemplate ($filename, false, null, 0);
	}

	protected function getTemplate()
	{
		return $this->tpl->getTemplate();
	}

	protected function addModule ($id, $position = 'center', $order = null)
	{
		if ($module = $this->model->getModuleById ($id)) {

			if (empty($this->content[$position])) {
				$this->content[$position] = array();
			}
			$plugin['position'] = $position;
			if (is_null($order)) {
				$this->content[$position][] = $module;
			} else {
				$start = array ();
				$end = array ();
				foreach ($this->content[$position] as $item) {
					if ($item['ord'] < $order) {
						$start[] = $item;
					} else {
						$end[] = $item;
					}
				}
//				$start = array_slice($this->content[$position], 0, $order);
//				$end = array_slice($this->content[$position], $order);
				$this->content[$position] = $start;
				$this->content[$position][] = $module;
				foreach ($end as $content) {
					$this->content[$position][] = $content;
				}
			}
		}
	}

	protected function buildModules ()
	{
		$output = '';
		$modules = array ();

		foreach ($this->content as $position => $contents) {

			foreach ($contents as $content) {
				if ($content['module']) {
					$module = new $content['module']($this->page, $content);
					$module->setWireframe ($this);
					$module->build ();

					if (!$module->skip) {
						if ($module->xclass) $content['xclass'] = $module->xclass;
						$content['output'] = $module->tpl->fetch($module->tpl->getTemplate());
						$output.= $content['output'];
						$modules[$position][] = $content;
					}

				} else {
					//	TODO: exception
//					new CustomException ("ava");
//					echo ("Error: missing plugin " . $content['module']);
				}
			}
		}
		$this->tpl->assign('modules', $modules);
		return $output;
	}

	public function addTitle ($title)
	{
		$this->page_title[] = $title;
	}

	public function addKeywords ($keywords)
	{
		$this->page_keywords[] = $keywords;
	}

	public function addDescription ($description)
	{
		$this->page_description[] = $description;
	}

	public function addCSS ($css, $module = null, $media = 'screen') {
		if (!empty ($module)) {
			$new_css = Config::get ('module_dir') . $module . '/css/' . $css;
		} else {
			$new_css = Config::get ('css_dir') . $css;
		}

		if (!isset ($this->css[$media]) || !in_array ($new_css, $this->css[$media])) {
			$this->css[$media][] = $new_css;
		}
	}

	public function addJS ($js, $module = null) {
		if (!empty ($module)) {
			$new_js = Config::get ('module_dir') . $module . '/js/' . $js;
		} else {
			$new_js = Config::get ('js_dir') . $js;
		}

		if (!in_array ($new_js, $this->js)) {
			$this->js[] = $new_js;
		}
	}

	public function getCSS () {
		$output = '';

		if (Config::get ('compact_css')) {
			foreach ($this->css as $media => $cssArr) {
				$cached_css = $media;
				foreach ($cssArr as $css) {
					if (file_exists ($css)) {
						$cached_css.= $css . filemtime ($css);
					}
				}
				$cached_css = 'media/cache/' . md5 ($cached_css) . '.css';
				if (!is_file ($cached_css)) {
					$fc = '';
					foreach ($cssArr as $css) {
						if (file_exists ($css)) {
							$fc.= str_replace(array("\t","\n","\r",': ',', ',' {', '  '), array('','','',':',',','{', ''), file_get_contents ($css)) . "\n";
						}
					}
					file_put_contents ($cached_css, $fc);
				}
				$output.= '<link type="text/css" href="/' . $cached_css . '" media="' . $media . '" rel="stylesheet" />' . "\n";
			}

		} else {
			foreach ($this->css as $media => $cssArr) {
				foreach ($cssArr as $css) {
					if (file_exists ($css)) {
						$output.= '<link type="text/css" href="/' . $css . '" media="' . $media . '" rel="stylesheet" />' . "\n";
					}
				}
			}
		}

		return $output;
	}

	public function getJS () {
		$output = '';

		if (Config::get ('compact_js')) {
			$cached_js = '';
			foreach ($this->js as $js) {
				if (file_exists ($js)) {
					$cached_js.= $js . filemtime ($js);
				}
			}
			$cached_js = 'media/cache/' . md5 ($cached_js) . '.js';
			if (!is_file ($cached_js)) {
				$fc = '';
				foreach ($this->js as $js) {
					if (file_exists ($js)) {
						$fc.= ';' . file_get_contents ($js) . ";\n";
					}
				}
				file_put_contents ($cached_js, $fc);
			}
			$output.= '<script src="/' . $cached_js . '" type="text/javascript"></script>' . "\n";

		} else {
			foreach ($this->js as $js) {
				if (file_exists ($js)) {
					$output.= '<script src="/' . $js . '" type="text/javascript"></script>' . "\n";
				}
			}
		}

		return $output;
	}

	public function addPackage ($package)
	{
		if (!empty ($this->config['packages'][$package])) {
			if (!empty ($this->config['packages'][$package]['js'])) {
				foreach ($this->config['packages'][$package]['js'] as $js) {
					$this->addJS ($js);
				}
			}
			if (!empty ($this->config['packages'][$package]['css'])) {
				foreach ($this->config['packages'][$package]['css'] as $css) {
					$this->addCSS ($css);
				}
			}
		}
	}

	protected function getClassFileName($plugin)
	{
		return strtolower (preg_replace('/([a-z0-9])([A-Z])/', '\\1_\\2', $plugin));
	}

}

class WireframeAjax extends Wireframe {

	protected $response = null;

	public function build ()
	{
		$this->setup ();

		// create content
		$module_id = Params::$_POST['module_id'] ? Params::$_POST['module_id'] : Params::$_GET['module_id'];
		$module = Params::$_POST['module'] ? Params::$_POST['module'] : Params::$_GET['module'];
		$mode = Params::$_POST['mode'] ? Params::$_POST['mode'] : Params::$_GET['mode'];
		$content =	array(
			'module_id' => $module_id ? $module_id : $this->getClassFileName ($module),
			'module'	=> $module,
			'mode'		=> $mode,
			'ajax'		=> true,
		);

		// create plugin
		$ajax_module = $content['module'] . 'Ajax';
		$module = new $ajax_module ($this->page, $content);
		if ($module instanceof Module) {
			$module->setWireframe ($this);
			$this->response = $module->buildAjax();
		} else {
			$method = 'build' . $mode;
			$this->response = $module->$method ();
		}
	}

	public function fetch()
	{
		return $this->response;
	}

}
