<?php

class WireframeIndexModel extends CProjectModel {

	public function getHomeMenu ()
	{
		$default = Config::get ("default_page");
		return $this->pages->getPageById($default['id']);
	}

	public function getSettings ()
	{
		$sql = "
			SELECT
				*
			FROM
				_settings
			ORDER BY
				id ASC
			LIMIT 1
		";
		return $this->db->prepare ($sql)->getRow ();
	}

}
