<?php

/**
 * Returns locale and replaces placeholders
 */
function Dwoo_Plugin_locale (Dwoo $dwoo, $locale = null, $params = array (), $assign = null)
{
	$output = Config::get ("locale")->get ($locale);
	foreach ($params as $key => $item) {
		$output = str_ireplace ('[%' . $key . '%]', $item, $output);
	}

	if ($assign !== null) {
		$dwoo->assignInScope($output, $assign);
	} else {
		return $output;
	}
}
