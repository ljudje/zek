<?php

class WireframeIndex extends Wireframe {

	protected $ogData = null;
	protected $bcoption = null;
	protected $canonical = null;
	protected $currentGroup = null;

	public function __construct ($page, $content)
	{
		$this->page = $page;
		$this->content = $content;

		$this->setup();

		// wireframe model
		$this->model = new WireframeIndexModel;
	}

	public function build ()
	{
		//	Detect mobile device
		Config::set ('is_mobile', Utils::isMobileBrowser ());

		$home_page = $this->model->getHomeMenu ();
		$this->assign ("home_page", $home_page);

		$project_home = $this->model->getPageByModule ('content_feature');
		$this->model->fetchGroups ('project', 0 , 'project');

		//	Menu
		$menu = $this->pages->getNavigation();

		//	Checks for "level_down" and assigns URL of next item
		foreach ($menu as $type => $items) {
			foreach ($items as $key => $item) {
				foreach ($item as $idx => $mitem) {
					if (isset ($mitem['level_down']) && $mitem['level_down'] == 1) {
						if (isset ($items[$mitem['id']])) {
							$tmp = current ($items[$mitem['id']]);
							$menu[$type][$key][$idx]['url'] = $tmp['url'];
						}
					}
				}
			}
		}
		$this->assign ("menu", $menu);
		$this->assign ("page", $this->page);

		Config::set ('menu', $menu);

		$this->buildModules();

		//	Breadcrumbs
		$parent_pages = $this->pages->getParentPages ();
		$this->assign ("parent_pages", $parent_pages);
	}

	public function fetch($setHeader = true)
	{
		if (!empty ($this->currentGroup['page_ad_keyword'])) {
			$this->assign ('page_ad_keyword', $this->currentGroup['page_ad_keyword']);
		} elseif (!empty ($this->page['page_ad_keyword'])) {
			$this->assign ('page_ad_keyword', $this->page['page_ad_keyword']);
		} else {
			$this->assign ('page_ad_keyword', 'EMPTY');
		}

		$this->assign('page_title', implode (" | ", array_reverse ($this->page_title)));
		$this->assign('page_keywords', trim ($this->page['page_keywords'] . " " . implode (" ", array_reverse ($this->page_keywords))));
		$this->assign('page_description', trim ($this->page['page_description'] . " " . implode (" ", array_reverse ($this->page_description))));
		$this->assign('og_data', $this->ogData);
		$this->assign('bc_option', $this->bcoption);
		$this->assign('canonical', $this->canonical);

		$this->assign ("css", $this->getCSS ());
		$this->assign ("js", $this->getJS ());

		$output = $this->tpl->fetch ($this->getTemplate());

		if ($setHeader) header('Content-type: text/html; charset=utf-8');

		return $output;
	}

	public function addOgData ($data)
	{
		$this->ogData = $data;
	}

	public function addBCOption ($title)
	{
		$this->bcoption = $title;
	}

	public function addCanonicalUrl ($url)
	{
		$this->canonical = $url;
	}

}
