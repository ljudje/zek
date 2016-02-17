<?php

class ProjectModel extends Model {

	public function getPages ()
	{
		if ($array = Cache::get ('sys_pages')) {
			return $array;

		} else {
			$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
			$sql = "
				SELECT
					{$db_prefix}page.*,
					{$db_prefix}page.id,
					{$db_prefix}page.page_id,
					{$db_prefix}page.type,
					{$db_prefix}page.wireframe,
					{t:{$db_prefix}page}.title,
					{t:{$db_prefix}page}.title AS url_title,
					{t:{$db_prefix}page}.url_external
				FROM
					{$db_prefix}page
				{cj:{$db_prefix}page}
				WHERE
					{$db_prefix}page.hidden = 0
				ORDER BY
					{$db_prefix}page.ord ASC
			";

			$array = $this->translate($sql)->getAll();
			Cache::set ('sys_pages', $array, true, Config::get ('memcache_timeout'));
		}
		return $array;
	}

	public function getContents ($page_id)
	{
		if ($array = Cache::get ('sys_content' . $page_id)) {
			return $array;
		} else {
			$array = array ();
			$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
			$sql = "
				SELECT
					{$db_prefix}content.*,
					{t:{$db_prefix}content}.title,
					{t:{$db_prefix}content}.lead,
					{t:{$db_prefix}content}.content,
					module.id AS module_id,
					module.class AS module,
					module.mode AS mode
				FROM
					{$db_prefix}content
				{j:{$db_prefix}content}
				LEFT JOIN
					{$db_prefix}module AS module ON {$db_prefix}content.module_id = module.id
				WHERE
					{$db_prefix}content.hidden = 0
					AND
					({$db_prefix}content.page_id = ? OR {$db_prefix}content.page_id IS NULL)
				ORDER BY
					{$db_prefix}content.position ASC,
					{$db_prefix}content.ord ASC,
					{$db_prefix}content.id ASC
				";

			$tmp = $this->translate ($sql, array ($page_id))->getAll ();
			foreach ($tmp as $item) {
				$array[$item['position']][] = $item;
			}

			Cache::set ('sys_content' . $page_id, $array, true, Config::get ('memcache_timeout'));
		}
		return $array;
	}

	public function getPageByModule ($module_id)
	{
		$key = "ProjectModel::getPageByModule('{$module_id}')";

		if ($array = Cache::get ($key)) {
			return $array;
		} else {
			$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
			$sql = "
				SELECT
					page.id
				FROM
					{$db_prefix}content AS content
				LEFT JOIN
					{$db_prefix}page AS page
					ON
					content.page_id = page.id
				WHERE
					content.module_id = '{$module_id}'
					AND
					NOT content.hidden
				ORDER BY
					content.page_id DESC
				LIMIT 1
			";

			$page_id =  $this->db->prepare($sql)->getRow('id');
			if ($page_id) {
				$page = $this->pages->getPageById($page_id);
				Cache::set ($key, $page, true, Config::get ('memcache_timeout'));
				return $page;
			}
		}
	}

	function getModuleById ($id)
	{
		if ($array = Cache::get ('sys_module_by_id_' . $id)) {
			return $array;
		} else {
			$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
			$sql = "
				SELECT
					id AS module_id,
					class AS module,
					mode
				FROM
					{$db_prefix}module AS module
				WHERE
					id = '". $id ."'";

			$array = $this->db->prepare($sql)->getRow();

			Cache::set ('sys_module_by_id_' . $id, $array, true, Config::get ('memcache_timeout'));
		}
		return $array;
	}

}
