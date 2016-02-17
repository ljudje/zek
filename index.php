<?php

function sanitize_output($buffer) {
	$search = array(
		'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
		'/[^\S ]+\</s',  // strip whitespaces before tags, except space
		'/(\s)+/s'       // shorten multiple whitespace sequences
	);
	$replace = array(
		'>',
		'<',
		'\\1'
	);
	$buffer = preg_replace($search, $replace, $buffer);
	return $buffer;
}

ini_set ("display_errors", 1);
ini_set ("display_startup_errors", 1);

$start = microtime (true);

//ob_start("sanitize_output");
//		ob_start("ob_gzhandler");

require_once("config/config.php");
require_once("core/core.php");
require_once("project/project_model.php");
require_once("project/project.php");

$project = new CProject;
echo $project->build();

//	Benchmark purposes only
if (Params::$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' && (isset ($_GET['debug']) || Config::get ('debug')))
	echo '
			<div style="position:absolute; top:0px; right:0px; width: 160px; background: #000; color: #fff; padding: 5px;">
				Time: ' . (microtime (true) - $start) . '<br />
				Memory: ' . round((memory_get_usage() / 1024 / 1024), 3) . 'MB<br />
				Peak mem: ' . round((memory_get_peak_usage() / 1024 / 1024), 3) . 'MB<br />
				Queries: ' . Db::$cnt . '<br />
			</div>';
//ob_end_flush();
