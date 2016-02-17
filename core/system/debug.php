<?php

// disable php html errors
ini_set('html_errors', 0);

// set media
Debug::$css			= '/media/css/debug.css';

class Debug {

	public static $css		 = '';
	public static $js		 = '';
	public static $close_img = '';
	public static $activated = false;

	public static function dump($var, $title = null)
	{
		ob_start();
		var_dump($var);
		$dump = ob_get_contents();
		ob_clean();

		// get code
		$code = self::getFileLine(self::getTrace(1, 'file'), self::getTrace(1, 'line'));
		if (preg_match('/(debug|diebug)\((.+)\);/i', trim($code), $match)) {
			$var_name = $match[2];
		} else {
			$var_name = '$var';
		}

		// set title
		if (!$title) {
			$title = $var_name;
		}

		// parse var
		$var = self::getVar($dump, array($var_name));
		$output = '
			<div class="debug">
				'. self::printCloseImg() .'
				<div class="debug_info">
					<div class="debug_title">'. $title .'</div>
					'. self::showTrace(self::getTrace()) .'
				</div>
				<div class="'. $var['type'] .'">'. $var['output'] . '</div>
				<div class="spacer">&nbsp;</div>
			</div>
		';
		return self::printCSS() . $output;
	}

	public static function trace()
	{
		self::printCSS();
	}

	public static function printCSS()
	{
		if (!self::$activated) {
			self::$activated = true;
			return '<link type="text/css" href="'. self::$css .'" rel="stylesheet" />';
		}
	}

	public static function printCloseImg()
	{
		return '<img class="debug_close" onclick="this.parentNode.style.display = \'none\';" src="/media/dsg/debug_close.gif" />';
	}

	public static function showTrace($trace)
	{
		$output = '
			<table class="debug_trace">
				<tr>
					<th>#</th>
					<th>Function</th>
					<th>File</th>
				</tr>
		';

		// reverse
		$trace = array_reverse($trace, true);
		$cnt = 1;
		foreach ($trace as $index => $step) {
			if ($step['class'] != 'Debug') {
				$output.= '
					<tr>
						<td>'. $cnt .'</td>
						<td>'. ($step['class'] ? $step['class'] . '->' : '') . '<span class="function">' . $step['function'] . '()</span></td>
						<td>
				';
				if (!empty($step['file'])) {
					$output.= Debug::formatFilename($step['file']).' <span class="size">('. $step['line'] .')</span>';
				}
				$output.= '
						</td>
					</tr>
				';
				$cnt++;
			} else {
				break;
			}
		}
		$output.= '</table>';
		return $output;
	}

	public static function formatFilename($file)
	{
		$script = str_replace("/", "\\", $_SERVER['SCRIPT_FILENAME']);
		$root_dir_parts = explode("\\", strrev($script), 2);
		$root_dir = strrev($root_dir_parts[1]) . "\\";
		return substr($file, strlen($root_dir));
	}

	public static function getTrace($index = null, $var = null)
	{
		// get trace
		$debug_back_trace = debug_backtrace();

		// remove debug step
		array_shift($debug_back_trace);
		foreach ($debug_back_trace as $trace_index => $step) {
			$trace[$trace_index] = array(
				'function'	=> isset($step['function']) ? $step['function'] : null,
				'class'		=> isset($step['class']) ? $step['class'] : null,
				'file'		=> isset($step['file']) ? $step['file'] : null,
				'line'		=> isset($step['line']) ? $step['line'] : null,
			);
		}
		if (is_null($index)) {
			return $trace;
		} else if (!empty($trace[$index])) {
			if (is_null($var)) {
				return $trace[$index];
			} else if (!empty($trace[$index][$var])) {
				return $trace[$index][$var];
			}
		}
	}

	private static function getFileLine($file, $number)
	{
		$i = 0;
		$fp = fopen($file, 'r');
		while ($i < $number) {
			$line = fgets($fp);
			$i++;
		}
		fclose($fp);
		return $line;
	}

	private static function getVar($dump, $path = array(), $hide_value = 0)
	{
		// match type
		if (preg_match('/^(&)?([a-z]+)(\(([^\)]+)\))?(.*)$/is', $dump, $var)) {
			$type = strtolower($var[2]);
			$size = isset($var[4]) ? $var[4] : null;
			$content = isset($var[5]) ? trim($var[5]) : null;
			// check if string is numeric
			if ($type == 'string') {

				// remove quotes
				$numeric = str_replace('"', '', $content);
				if (self::isNumeric($numeric)) {
					$size = $numeric;
					$type = 'int';
				}
			}
			$printer = 'print' . $type;
			$output = self::$printer($size, $content, $path, $hide_value);

		// unknown type
		} else {
			$type = 'string';
			$output = self::printString(strlen($dump), $dump, $path, $hide_value);
		}
		return array('type' => $type, 'output' => $output);
	}

	private static function printNULL()
	{
		return 'NULL';
	}

	private static function printBool($content)
	{
		return strtoupper($content);
	}

	private static function printInt($content)
	{
		return $content;
	}

	private static function printFloat($content)
	{
		return $content;
	}

	private static function printString($size, $content, $path, $hide_value)
	{
		if ($size) {
			$content = str_replace('"', '', $content);
			if ($hide_value) {
				$string = preg_replace('/./i', '*', $content);
			} else {
				$string = nl2br(htmlspecialchars(wordwrap($content, 50, "\n", true)));
			}
			return $string . ' <span class="size">('. $size .')</span>';
		} else {
			return '<span class="empty">/</span';
		}
	}

	private static function printResource($size, $content)
	{
		preg_match('/\((.+)\)/i', $content, $type);
		return strtoupper($type[1]) . ' ('. $size .')';
	}

	private static function printArray($size, $content, $path)
	{
		$data = explode("\n". sprintf('%'. (sizeof($path) * 2) .'s', '') . '[', substr($content, 1, -2));
		$output = '<h3>Array <span class="size">('. $size .')</span> <span class="path">'. implode('', $path) .'</span></h3> {<table>';
		for ($i = 1; $i <= $size; $i++) {
			$row = explode(']=>', $data[$i], 2);
			$key = str_replace('"', '', $row[0]);
			$item_path = array_merge($path, array('['. (self::isNumeric($key) ? $key : "'$key'") .']'));
			$var = self::getVar(trim($row[1]), $item_path, self::hideValue($key));
			if (self::isNumeric($key)) {
				$key = '<span class="numeric">'. $key .'</span>';
			}
			$output.= '
				<tr>
					<th>['. $key .']</th>
					<td class="'. $var['type'] .'"><span class="equals">=</span> '. $var['output'] .'</td>
				</tr>
			';
		}
		$output.= '</table>}<br /><br />';
		return $output;
	}

	private static function printObject($name, $content, $path)
	{
		$data = explode("\n". sprintf('%'. (sizeof($path) * 2) .'s', '') . '[', substr($content, 1, -2));
		preg_match('/^#([0-9]+) \(([0-9]+)\) \{(.+)\}$/is', $content, $object);

		$output = '<h3><strong>'. $name .'</strong> object #'. $object[1] .' <span class="path">'. implode('', $path) .'</span></h3> {<table>';
		for ($i = 1; $i <= $object[2]; $i++) {
			$row = explode(']=>', $data[$i], 2);
			preg_match('/^([^:]+)(:([A-Z]+))?$/i', str_replace('"', '', $row[0]), $item);
			$member = $item[1];
			$visibility = isset($item[3]) ? $item[3] : 'public';
			$item_path = array_merge($path, array('->' . $member));
			$var = self::getVar(trim($row[1]), $item_path, self::hideValue($member));
			$output.= '
				<tr>
					<th><em>'. $visibility .'</em>&nbsp;$'. $member .'</th>
					<td class="'. $var['type'] .'"><span class="equals">=</span> '. $var['output'] .'</td>
				</tr>
			';
		}
		$output.= '</table>}<br /><br />';
		return $output;
	}

	private static function hideValue($var_name)
	{
		return preg_match('/pass/i', $var_name);
	}

	private static function isNumeric($value)
	{
		return preg_match('/^[0-9]+(\.[0-9]+)?$/', $value);
	}

}

function debug($var, $title = null, $print = true)
{
	$output = Debug::dump($var, $title);
	if ($print) {
		echo $output;
	} else {
		return $output;
	}
}

function diebug($var, $title = null)
{
	debug($var, $title, true);
	die();
}

//if (isset($_GET['debug'])) {
//	debug($_SERVER,  '$_SERVER');
//	debug($_SESSION, '$_SESSION');
//	debug($_COOKIE,  '$_COOKIE');
//	debug(ini_get_all(), 'php.ini');
//	die();
//}


//	https://github.com/tomasfejfar/enhanced-dump
/**
 * Simple variable dump.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function d($var, $label = null)
{
	echo '<div style="background:#f8f8f8;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
	if ($label) {
		echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
	}
	echo dtrace();
	echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
	var_dump($var);
	echo '</pre>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
}

/**
 * Dump variable and die.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function dd($var, $label = null)
{
	echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
	if ($label) {
		echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
	}
	echo dtrace();
	echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
	var_dump($var);
	echo '</pre>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	die();
}

/**
 * Dump variable as string.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function ds($var, $label = null)
{
	echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
	if ($label) {
		echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
	}
	echo dtrace();
	echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
	var_dump((string) $var);
	echo '</pre>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
}

/**
 * Dump variable as string and die.
 *
 * @param  mixed  $var   The variable to dump.
 * @param  string $label OPTIONAL Label to prepend to output.
 */
function dsd($x, $label = null)
{
	echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
	if ($label) {
		echo sprintf('<strong>%s</strong><br />', $label) . PHP_EOL;
	}
	echo dtrace();
	echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
	var_dump((string) $var);
	echo '</pre>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	die();
}

/**
 * Print peak memory usage.
 *
 */
function dmem()
{
	echo '<div style="background:#fafafa;margin:5px;padding:5px;border: solid grey 1px;">' . PHP_EOL;
	echo dtrace();
	echo '<pre style="margin:0px;padding:0px;">' . PHP_EOL;
	echo sprintf('%sK of %s', round(memory_get_peak_usage()/1024), ini_get('memory_limit'));
	echo '</pre>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
}

/**
 * Measure execution time.
 *
 * @param array $timers
 * @param type $status
 * @param type $label
 */
function dtimer(&$timers, $status = 0, $label = null)
{
	if (!is_array($timers) || $status === -1) {
		$timers = array();
	}
	$where = dtrace();
	if (null !== $label){
		$where = $label . ' - ' . $where;
	}


	$timers[] = array('where' => $where, 'time' => microtime(true));
	if ($status === 1) {
		echo '<table style="border-color: black;" border="1" cellpadding="3" cellspacing="0">';
		echo '<tr style="background-color:black;color:white;"><th>Trace</th><th>dT [ms]</th><th>dT(cumm) [ms]</th></tr>';
		$lastTime = $timers[0]['time'];
		$firstTime = $timers[0]['time'];
		foreach ($timers as $timer) {
			echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>',
				$timer['where'],
				sprintf('%01.6f',round(($timer['time'] - $lastTime)*1000,6)),
				sprintf('%01.6f',round(($timer['time'] - $firstTime)*1000,6))
			);
			$lastTime = $timer['time'];
		}
		echo '</table>';
	}
}

/**
 * Backtrace.
 *
 * @return string backtrace
 */
function dtrace()
{
	$bt = debug_backtrace();
	$trace = $bt[1];
	$line = $trace['line'];
	$file = basename($trace['file']);
	$function = $trace['function'];
	$class = (isset($bt[2]['class'])?$bt[2]['class']:basename($trace['file']));
	if (isset($bt[2]['class'])) {
		$type = $bt[2]['type'];
	} else {
		$type = ' ';
	}
	$function = isset($bt[2]['function']) ? $bt[2]['function'] : '';
	return sprintf('%s%s%s() line %s <small>(in %s)</small>',$class, $type, $function, $line, $file);
}
