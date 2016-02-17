<?php

class Content extends Module {

	public function buildFeature ()
	{
		if (Params::$_SERVER['REQUEST_URI'] != '/') {
			$project = $this->pages->getCurrentItem ('project');
			if (empty ($project)) {
				header('location: /');
			}
			$this->assign('active_project', $project);

			//	OGP
			$this->assign ('ogp_current_url', Config::get ('url') . Params::$_SERVER['REQUEST_URI']);
			$ogArray = array (
					'title'			=> $project['title'],
					'description'	=> $project['lead'],
					'type'			=> 'article',
					'url'			=> Config::get('url') . Params::$_SERVER['REQUEST_URI'],
					'site_name'		=> $this->locale['page_title']
			);
			if (!empty ($project['picture'])) {
				$image = Image::getInstance();
				$ogArray['image'] = Config::get('url') . $image->img($project['picture'], 'project/', 300, 300);
			}

			$this->wireframe->addOgData ($ogArray);
			$this->wireframe->page_description[] = $project['lead'];

		}

		$projects = $this->model->getProjects ();
		$this->assign ('projects', $projects);

		$this->wireframe->addPackage('slick');
	}

	public function build404 ()
	{

	}

}
