<?php

class Utils {

	public static function getFileData ($file, $dir)
	{
		$array = array ();
		if (file_exists ($dir . $file)) {
			$array['size'] = filesize ($dir . $file);
			$array['type'] = substr ($file, strrpos ($file, ".") + 1);
			$array['size_h'] = Utils::formatBytes ($array['size'], 2);
		}

		return $array;
	}

	public static function formatBytes($bytes, $precision = 2)
	{
	    $units = array('B', 'kB', 'MB', 'GB', 'TB');

	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);

	    $bytes /= pow(1024, $pow);

	    return round($bytes, $precision) . ' ' . $units[$pow];
	}

	public static function date_format ($value, $format='%b %e, %Y', $default=null)
	{
		if (!empty($value)) {
			// convert if it's not a valid unix timestamp
			if (preg_match('#^-?\d{1,10}$#', $value) === 0) {
				$value = strtotime($value);
			}
		} elseif (!empty($default)) {
			// convert if it's not a valid unix timestamp
			if (preg_match('#^-?\d{1,10}$#', $default) === 0) {
				$value = strtotime($default);
			} else {
				$value = $default;
			}
		} else {
			return '';
		}

		// Credits for that windows compat block to Monte Ohrt who made smarty's date_format plugin
		if (DIRECTORY_SEPARATOR == '\\') {
			$_win_from = array('%D',       '%h', '%n', '%r',          '%R',    '%t', '%T');
			$_win_to   = array('%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S');
			if (strpos($format, '%e') !== false) {
				$_win_from[] = '%e';
				$_win_to[]   = sprintf('%\' 2d', date('j', $value));
			}
			if (strpos($format, '%l') !== false) {
				$_win_from[] = '%l';
				$_win_to[]   = sprintf('%\' 2d', date('h', $value));
			}
			$format = str_replace($_win_from, $_win_to, $format);
		}
		return strftime($format, $value);
	}

	public static function truncate ($content, $limit = 100, $char = ' ', $endchar = '...') {
		$output = '';

		if (strlen ($content) > $limit) {
			$output = substr ($content, 0, strripos (substr ($content, 0, $limit), $char));

			if (stripos ($output, '</p>')) {
				$output = substr ($output, 0, stripos ($output, '</p>'));
			} else {
				$output.= $endchar;
			}
		} else {
			$output = $content;
		}

		return strip_tags ($output);
	}

	public static function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
		foreach ($array as $subarray) {
			$keys[] = $subarray[$subkey];
		}
		array_multisort($keys, $sortType, $array);
	}

	public static function matchArrayKeys ($data, $string)
	{
		$stockList = array();  //Your list of "stocks" indexed by the number found at the end of "stock"

		foreach ($data as $stockKey => $stock) {
			$stockId = null;
			sscanf($stockKey, $string . "%d", $stockId);  // scan into a formatted string and return values passed by reference
			if ($stockId !== false && !empty ($stockId))
				$stockList[$stockId] = $stock;
		}

		return $stockList;
	}

	public static function matchArraySKeys ($data, $string, $returnAll = false)
	{
		$stockList = array();  //Your list of "stocks" indexed by the number found at the end of "stock"

		foreach ($data as $stockKey => $stock) {
			$stockId = null;
			sscanf($stockKey, $string . "%s", $stockId);  // scan into a formatted string and return values passed by reference
			if ($stockId !== false && !empty ($stockId)) {
				if ($returnAll) {
					$stockList[$stockKey] = $stock;
				} else {
					$stockList[$stockId] = $stock;
				}
			}

		}

		return $stockList;
	}

	// Generates a strong password of N length containing at least one lower case letter,
	// one uppercase letter, one digit, and one special character. The remaining characters
	// in the password are chosen at random from those four sets.
	//
	// The available characters in each set are user friendly - there are no ambiguous
	// characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
	// makes it much easier for users to manually type or speak their passwords.
	//
	// Note: the $add_dashes option will increase the length of the password by
	// floor(sqrt(N)) characters.

	public static function generateStrongPassword ($length = 9, $add_dashes = false, $available_sets = 'luds')
	{
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';

		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];

		$password = str_shuffle($password);

		if(!$add_dashes)
		return $password;

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	public static function generateUUID ()
	{
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}

	public static function isMobileBrowser ()
	{
		//	from: http://detectmobilebrowsers.com/
		$useragent = Params::$_SERVER['HTTP_USER_AGENT'];
		return (bool)preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|meego.+mobile|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
	}

	public static function isFacebook ()
	{
		//	from: http://www.creativewhim.com/web-design/6-html5-validation-errors-and-how-to-fix-them
		if(!empty ($_SERVER["HTTP_USER_AGENT"]) && !(stristr($_SERVER["HTTP_USER_AGENT"],'facebook') === FALSE)) {
			return true;
		}
	}

	/*
	NAME        : autolink()
	VERSION     : 1.0
	AUTHOR      : J de Silva
	DESCRIPTION : returns VOID; handles converting
	URLs into clickable links off a string.
	TYPE        : functions
	======================================*/
	public static function autolink (&$text, $target='_blank', $nofollow=true )
	{
		// grab anything that looks like a URL...
		$urls  =  self::_autolink_find_URLS( $text );
		if( !empty($urls) ) // i.e. there were some URLS found in the text
		{
			array_walk( $urls, array ('self', '_autolink_create_html_tags'), array('target'=>$target, 'nofollow'=>$nofollow) );
			$text  =  strtr( $text, $urls );
		}
	}

	private static function _autolink_find_URLS( $text )
	{
		// build the patterns
		$scheme         =       '(http:\/\/|https:\/\/)';
		$www            =       'www\.';
		$ip             =       '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';
		$subdomain      =       '[-a-z0-9_]+\.';
		$name           =       '[a-z][-a-z0-9]+\.';
		$tld            =       '[a-z]+(\.[a-z]{2,2})?';
		$the_rest       =       '\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1}';
		$pattern        =       "$scheme?(?(1)($ip|($subdomain)?$name$tld)|($www$name$tld))$the_rest";

		$pattern        =       '/'.$pattern.'/is';
		$c              =       preg_match_all( $pattern, $text, $m );
		unset( $text, $scheme, $www, $ip, $subdomain, $name, $tld, $the_rest, $pattern );
		if ($c) {
			return( array_flip($m[0]) );
		}
		return (array());
	}

	private static function _autolink_create_html_tags( &$value, $key, $other=null )
	{
		$target = $nofollow = null;
		if( is_array($other) )
		{
			$link = $key;
			if (stripos($key, 'http') !== 0) $key = 'http://' . $key;
			$target      =  ( $other['target']   ? " target=\"$other[target]\"" : null );
			// see: http://www.google.com/googleblog/2005/01/preventing-comment-spam.html
			$nofollow    =  ( $other['nofollow'] ? ' rel="nofollow"'            : null );
		}
		$value = "<a href=\"$key\"$target$nofollow>$link</a>";
	}

	public static function httpGet ($url)
	{
// 		return file_get_contents ($url);
		$ch = curl_init();
		// set the target url
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// 		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$response = curl_exec($ch);
		curl_close ($ch);
		return $response;
	}

	public static function searchArray ($array, $key, $value)
	{
	    $results = array();

	    if (is_array($array))
	    {
	        if (isset($array[$key]) && $array[$key] == $value)
	            $results[] = $array;

	        foreach ($array as $subarray)
	            $results = array_merge($results, Utils::searchArray ($subarray, $key, $value));
	    }

	    return $results;
	}

	public static function in_array_r ($needle, $haystack, $strict = false) {
		foreach ($haystack as $key => $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && Utils::in_array_r ($needle, $item, $strict))) {
				return $key;
			}
		}

		return false;
	}

	public static function hex2rgb( $colour ) {
		if ( $colour[0] == '#' ) {
			$colour = substr( $colour, 1 );
		}
		if ( strlen( $colour ) == 6 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} elseif ( strlen( $colour ) == 3 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		} else {
			return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}

	/**
	 * Verbose Beautified Date Range
	 *
	 * @access public
	 * @param mixed $start_date
	 * @param mixed $end_date
	 * @return $date_range (beautified date range)
	 *
	 * @author Jon Brown <jb@9seeds.com---->
	 * @since 1.0
	 */

	public static function prettyDate ($start_date = '', $end_date = '', $fullmonthname = false) {

		$start_date = strtotime ($start_date);
		$end_date = strtotime ($end_date);

		$date_range = '';
		$_month = ($fullmonthname) ? " %B " : "%m.";

		// If only one date, or dates are the same set to FULL verbose date
		if ( empty($start_date) || empty($end_date) || ( date('FjY',$start_date) == date('FjY',$end_date) ) ) { // FjY == accounts for same day, different time
			$start_date_pretty = strftime( '%e.' . $_month . '%Y', $start_date );
			$end_date_pretty = strftime( '%e.' . $_month . '%Y', $end_date );
		} else {
			// Setup basic dates
			$start_date_pretty = strftime( '%e.', $start_date );
			$end_date_pretty = strftime( '%e.' . $_month . '%Y', $end_date );
			// If years differ add suffix and year to start_date
			if ( date('Y',$start_date) != date('Y',$end_date) ) {
				$start_date_pretty .= strftime( $_month . '%Y', $start_date );
			}

			// If months differ add suffix and year to end_date
			if ( date('F',$start_date) != date('F',$end_date) ) {
				$start_date_pretty.= strftime( $_month, $start_date);
//				$end_date_pretty = strftime( '%B ', $end_date) . $end_date_pretty;
			}
		}

		// build date_range return string
		if( ! empty( $start_date ) ) {
			$date_range .= $start_date_pretty;
		}

		// check if there is an end date and append if not identical
		if( ! empty( $end_date ) ) {
			if( $end_date_pretty != $start_date_pretty ) {
				$date_range .= ' - ' . $end_date_pretty;
			}
		}
		return $date_range;
	}


	public static function parseLinks ($text)
	{
		return preg_replace_callback ('/((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<;]*)/is', array('Utils', 'parseLinksAndShortenLinkText'), $text);
	}

	private static function parseLinksAndShortenLinkText ($matches, $maxlength = 30)
	{
		/* FIRST CHECK WE HAVE HTTP AT START OF URL, IT'S NEEDED! */
		if (stripos ( $matches[1] , 'http') === false ){
			$url = 'http://'.$matches[1];
			$text_link = 'http://'.$matches[1];

		} else {
			$url = $matches[1];
			$text_link = $matches[1];

		}

		/* SECOND CHECK THAT URL IS NOT TOO LONG, ELSE SHORTEN */
		if (strlen ($text_link) > $maxlength ) {
			$text_link = substr ( $text_link, 0, $maxlength ) . '...';
		}

		/* RETURN LINKED URL */
		return '<a href="' . $url . '">' . $text_link . '</a>';
	}

}
