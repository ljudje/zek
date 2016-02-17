<?php

class Template {

	protected $dwoo				= null;
	protected $data				= null;
	protected $template			= null;
	protected $module_template	= null;
	protected $currentTemplate	= null;

	public function __construct($prefix = '')
	{
		$this->dwoo = new Dwoo();
		$this->data = new Dwoo_Data();
		$this->dwoo->setCompileDir ($prefix. Config::get ("template_compile_dir"));
		$this->dwoo->setCacheDir ($prefix. Config::get ("template_cache_dir"));

		if (isset ($_GET['clearcache'])) {
			$this->dwoo->clearCache();
		}

		return $this->data;
	}

	public function assign ($var, $value = null)
	{
		$this->data->assign ($var, $value);
	}

	public function setTemplate ($filename, $db = false, $dir = null, $cacheTime = null, $cacheId = null) {
		$cacheTime = $cacheTime !== null ? $cacheTime : Config::get ("template_cache_time");

		$this->currentTemplate = array (
			'filename'	=> $filename,
			'db'		=> $db,
			'dir'		=> $dir,
			'cacheTime'	=> $cacheTime,
			'cacheId'	=> $cacheId . Config::get('site_id')
		);
	}

	public function getTemplate () {
		return $this->template;
	}

	public function fetch () {
		if ($this->currentTemplate['db']) {
			$this->template = new Dwoo_Template_String ($this->currentTemplate['filename'], $this->currentTemplate['cacheTime'], $this->currentTemplate['cacheId']);
		} else {
			$this->template = new Dwoo_Template_File ($this->currentTemplate['filename'], $this->currentTemplate['cacheTime'], $this->currentTemplate['cacheId']);
			$this->template->setIncludePath (array (Config::get ('template_dir'), $this->currentTemplate['dir']));
		}

		return $this->dwoo->get ($this->template, $this->data);
	}

}
