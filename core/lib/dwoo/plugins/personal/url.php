<?php

/**
 * Generates an URL
 */
function Dwoo_Plugin_url (Dwoo $dwoo, $params = array(), $page = null, $include = true, $encode = false, $domain = false, $assign = null)
{
	$url = Config::get ("page")->getUrl ($params, $page, $include);
	if ($domain) $url = Config::get ('url') . $url;
	if ($encode) $url = urlencode ($url);

	if ($assign !== null) {
		$dwoo->assignInScope($url, $assign);
	} else {
		return $url;
	}
}
