<?php

class CProject extends Project {

	protected function setModel()
	{
		$this->model = new CProjectModel;
	}

	public function build ()
	{
		if (!$this->errors) {

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
					$page_error = $this->model->getPageByModule ('content_404');
					$contents = $this->model->getContents($page_error['id']);

					$this->wireframe = Wireframe::create($page_error, $contents);

					$this->wireframe->build();
					header('HTTP/1.0 404 Not Found');
					header("Status: 404 Not Found");
					return $this->wireframe->fetch(false);
// 					header ('Location: /');
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

		}

		return $output;
	}

}
