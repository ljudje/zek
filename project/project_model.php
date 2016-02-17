<?php

class CProjectModel extends ProjectModel {

	/*	COMMENTS	*/
	public function saveComment ($field, $id, $name, $comment, $parent_id = 0)
	{
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
		$sql = "
			INSERT INTO
				{$db_prefix}comment
			(comment_id, " . $field . ", name, content, date_published, user_id, user_ip)
			VALUES
			(?,?,?,?,NOW(),?,?)";

		$this->db->prepare ($sql, array ($parent_id, $id, $name, $comment, $this->user['id'], Params::$_SERVER['REMOTE_ADDR']))->run();
	}

	public function getComments ($field, $id)
	{
		$array = array ();
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
		$sql = "
			SELECT
				c.*,
				u.name as username
			FROM
				{$db_prefix}comment c
			LEFT JOIN
				user u
				ON
				u.id = c.user_id
			WHERE
				" . $field . " = ?
				AND
				NOT c.hidden
			ORDER BY
				c.date_published ASC";

		$tmp = $this->db->prepare ($sql, array ($id))->getAll ();
		foreach ($tmp as $item) {
			$array[$item['comment_id']][] = $item;
		}
		return $array;
	}

	public function getCommentForm ($article_id)
	{
		$review = false;
		$form = new FormHandler('comment_form', Params::$_SERVER['REQUEST_URI'] . "#comments", 'class="form"');
		$form->setFocus(false);

		$form->textField("Ime", 'comment_name', FH_TEXT);

		$form->setMask ('<div class="field eml"><label for="%name%">%title%%seperator%</label><div>%field% %error%</div></div>', false);
		$form->textField("Email", 'comment_email', null);
		$form->textArea("Komentar", "comment_content", FH_TEXT);
		$form->hiddenField("aid", $article_id);
		$form->hiddenField("rid", 0);

		$form->submitButton("Pošlji", "comment_submit", 'onclick="Comments.submit(); return false;"; class="formbutton"');

		return $form;
	}

	/*	MEDIA	*/
	public function getMedia ($field, $id)
	{
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
		$sql = "
			SELECT
				{$db_prefix}media.id,
				{$db_prefix}media.picture,
				{$db_prefix}media.video,
				{t:{$db_prefix}media}.title,
				" . $field . " as parent_id
			FROM
				{$db_prefix}media
				{cj:{$db_prefix}media}
			WHERE
				{$db_prefix}media." . $field . " = ?
				AND
				NOT {$db_prefix}media.hidden
			ORDER BY
				{$db_prefix}media.ord ASC,
				{$db_prefix}media.id ASC";

		$tmp = $this->translate ($sql, array ($id))->getAll ();
		$array = array ();
		foreach ($tmp as $item) {
			// Check for video
			if (preg_match ('/^\d+$/is', $item['video'])) {
				$item['is_vimeo'] = true;
			} elseif (!empty ($item['video'])) {
				$item['is_youtube'] = true;
			}
			$array[$item['id']] = $item;
		}

		return $array;
	}

	public function getFiles ($field, $id)
	{
		$array = array ();
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
		$sql = "
			SELECT
				{$db_prefix}file.*
			FROM
				{$db_prefix}file
			WHERE
				" . $field . " = ?
				AND
				NOT {$db_prefix}file.hidden
			ORDER BY
				ord ASC,
				id ASC";

		$array = $this->db->prepare ($sql, array ($id))->getAll ();
		foreach ($array as &$item) {
			$item['meta'] = Utils::getFileData ($item['file'], 'media/uploads/file/');
		}

		return $array;
	}

	public function sendMail ($template, $user, $vars, $subject_replace = '')
	{
		$mail = new PHPMailer ();
		$mail->CharSet = Config::get('mail_charset');

		$mail->From = $this->locale['system_email'];
		$mail->FromName = $this->locale['system_email_name'];
		$mail->Sender = $this->locale['system_email'];

		$mail->IsHTML(true);

		//	To
		if (is_array($user)) {
			if (isset($user['email']) AND isset($user['name'])) {
				$mail->AddAddress($user['email'], $user['name']);
			} else {
				$mail->AddAddress($user['email']);
			}
		} elseif (is_string($user)) {
			$mail->AddAddress($user);
		} else {
			$mail->AddAddress($mail->From, $mail->FromName);
		}

		//	Mail
		$tpl = $this->getTemplate ($template);
		$this->tpl = new Template;

		$this->tpl->setTemplate ($tpl['content'], true);
		if ($vars) {
			foreach ($vars as $name => $var) {
				$this->tpl->assign($name, $var);
			}
		}

		$mail_content = $this->tpl->fetch ($tpl['content']);

		$subject = (!empty ($subject_replace)) ? str_replace ('_REPLACE_', $subject_replace, $tpl['title']) : $tpl['title'];

		$this->wrapTpl = new Template;
		$this->wrapTpl->setTemplate ('inc/mail.tpl');
		$this->wrapTpl->assign ('content', $mail_content);
		$this->wrapTpl->assign ('title', $subject);
		$this->wrapTpl->assign ('locale', $this->locale);

		$mail->Subject = $subject;
		$body = $this->wrapTpl->fetch ();
		$mail->MsgHTML ($body, './');

		$sent = $mail->Send ();

		return $sent;
	}

	public function getTemplate ($template)
	{
		$db_prefix = Config::get ('db_prefix') . Config::get ('db_systables_prefix');
		$sql = "
			SELECT
				*
			FROM
				{$db_prefix}template
			WHERE
				id = ?";

		$array = $this->db->prepare ($sql, array ($template))->getRow ();
		return $array;
	}

	//	Groups stuff :P
	public function fetchGroups($module, $page = null, $type = 'main', $parentOnly = false)
	{
		$groups = Config::get($module);
		if (!$groups) {
//			$plugin_page = ($page == null) ? $this->getPageByModule ($module) : $page;
			$groups = $this->getGroups ($page, $module, 0, $type, $parentOnly);
			Config::set ($module, $groups);
		}
		return $groups;
	}

	private function getGroups ($page, $module, $parent_id = 0, $type, $parentOnly = false)
	{
		$this->groups = array();
		$this->groups_by_id = array();

		if ($groups = Cache::get ('groups_' . $page['id'] . '_' . $module . '_' . $parent_id . '_' . $type . '_' . $parentOnly)) {
		} else {
			$sql = "
				SELECT
					" . $module . ".id,
					" . $module . "." . $module . "_id,
					{t:" . $module . "}.title,
					{t:" . $module . "}.title AS url_title,
					'" . $type . "' as type,
					'" . $module . "' as module,
					" . $module . ".ord,
					" . $module . ".*
				FROM " . $module . "
					{cj:" . $module . "}
				WHERE
					NOT " . $module . ".hidden
				ORDER BY
					" . $module . ".ord ASC,
					" . $module . ".id ASC
			";

			$groups = $this->translate ($sql)->getAll ();
			Cache::set ('groups_' . $page['id'] . '_' . $module . '_' . $parent_id . '_' . $type . '_' . $parentOnly, $groups, true, Config::get ('memcache_timeout'));
		}

		foreach ($groups as $group) {
			$parent = isset ($group[$module . '_id']) ? $group[$module . '_id'] : 0;
			if (($parentOnly && !$parent) || !$parentOnly)
				$this->groups[$parent][] = $group;
		}
		$this->buildGroups ($parent_id, $page, $module, $type);	//, $module

		return $this->groups;
	}

	private function buildGroups ($group_id = 0, $parent_group = null, $module = '', $type = 'main')
	{
		if (!empty($this->groups[$group_id])) {
			foreach ($this->groups[$group_id] as $key => &$group) {
				// add navigator item
				$group = $this->pages->addItem ($group, $parent_group, $type, $module);
				$this->groups_by_id[$group['id']] = $group;

				// calc totals
				$this->buildGroups($group['id'], $group, $module, $type);
			}
		}
	}

	public function getCurrentGroup ($module = 'main')
	{
		$group = Config::get("current_group_" . $module);
		if (!$group) {
			$group = $this->pages->getCurrentItem ($module);
			if ($group) {
				Config::set("current_group_" . $module, $group);
			}
		}
		return $group;
	}

//	OVERRIDES

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
					{t:{$db_prefix}page}.url_external,
					{t:{$db_prefix}page}.lead
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
				({$db_prefix}content.page_id = ? OR {$db_prefix}content.page_id IS NULL)
			ORDER BY
				{$db_prefix}content.position ASC,
				{$db_prefix}content.ord ASC,
				{$db_prefix}content.id ASC
			";

			$tmp = $this->translate ($sql, array ($page_id))->getAll ();
			foreach ($tmp as $item) {
				if ($item['hidden'] == 1) {
					if (!empty ($array[$item['position']])) {
						foreach ($array[$item['position']] as $key => &$tmpitm) {
							if ($tmpitm['module_id'] == $item['module_id'] && empty ($tmpitm['page_id'])) {
								unset ($array[$item['position']][$key]);
							}
						}
					}
				} else {
					$array[$item['position']][] = $item;
				}
			}

			Cache::set ('sys_content' . $page_id, $array, true, Config::get ('memcache_timeout'));
		}
		return $array;
	}

}
