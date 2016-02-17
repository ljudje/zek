<?php

class ContentModel extends CProjectModel {

	public function getProjects ()
	{
		$sql = "
			SELECT
				*
			FROM
				project
			WHERE
				NOT hidden
			ORDER BY
				ord ASC,
				id DESC
		";
		$array = $this->db->prepare ($sql)->getAll ();
		foreach ($array as &$item) {
			switch ($item['project_thumb_type']) {
				default: // default: 1
					$th_w = 400;
					$th_h = 400;
					break;
				case 2:
					$th_w = 400;
					$th_h = 800;
					break;
				case 3:
					$th_w = 800;
					$th_h = 400;
					break;
				case 4:
					$th_w = 800;
					$th_h = 800;
					break;
			}
			$item['thumb_w'] = $th_w;
			$item['thumb_h'] = $th_h;

			$item['class_w'] = $th_w / 100;
			$item['class_h'] = $th_h / 100;

			$menuitem = $this->pages->getItemById ($item['id'], 'project', 'project');

			$item['url'] = $menuitem['url'];

			$item['content'] = preg_replace('/src="(\/media.+?)"/is', 'data-ll="1" src="/media/dsg/placeholder.gif" data-src="\\1"', $item['content']);
			$item['content'] = preg_replace('/src="http/is', 'data-src="http', $item['content']);
			$item['content'] = str_replace('<script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>', '', $item['content']);
			$item['content'] = str_replace('<script async defer src="//platform.instagram.com/en_US/embeds.js"></script>', '', $item['content']);
		}
		return $array;
	}

}
