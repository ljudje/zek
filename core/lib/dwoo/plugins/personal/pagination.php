<?php

/**
 * Build a pagination
 */
function Dwoo_Plugin_pagination (Dwoo $dwoo, $data, $paging_pages = 5, $hash = '', $id = '', $include = true, $show_pages = false, $force_show = false, $class = '')
{
	$output = '';

	$use_ul = (bool)Config::get ('use_ul_pagination');
	$bootstrap = (int)Config::get ('bootstrap_version');
	$frontend_kit = Config::get ('frontend_kit');
	$frontend_version = (int)Config::get ('frontend_version');

	$page = Config::get ("page")->getCurrentPage ();
	$page_prev = Config::get ("locale")->get('paging_prev');
	$page_next = Config::get ("locale")->get('paging_next');

	$page_prev = $page_prev ? $page_prev : 'Prejšnja';
	$page_next = $page_next ? $page_next : 'Naslednja';

	$pages_plural = Config::get ("locale")->get('paging_pages_plural');
	$pages_plural = $pages_plural ? $pages_plural : 'i';

	$pages_title = Config::get ("locale")->get('paging_pages_title');
	$pages_title = $pages_title ? $pages_title : 'stran';

	if (!empty ($data['paging']) || $force_show) {

		$start_page = ($data['page'] <= ceil ($paging_pages / 2) || $paging_pages >= $data['pages']) ? 1 : (($data['page'] + floor ($paging_pages / 2) >= $data['pages']) ? (($data['pages'] - $paging_pages + 1 <= 0) ? 1 : $data['pages'] - $paging_pages + 1) : $data['page'] - floor ($paging_pages / 2));
		$max = ($data['pages'] < $paging_pages) ? $data['pages'] : (($start_page + $paging_pages > $data['pages']) ? $data['pages'] : $start_page + $paging_pages - 1);

		$prev = ($data['page'] > 1) ? $data['page'] - 1 : 1;
		$next = (($data['page'] + 1) <= $data['pages']) ? $data['page'] + 1 : $data['pages'];

// 		$recStart	= $data['limit'] * ($data['page'] - 1) + 1;
// 		$recEnd		= ($recStart + $data['limit'] > $data['pages']) ? $data['pages'] : $recStart + $data['limit'] - 1;

		if ($bootstrap == 3) {
			$output .= '
			<ul class="pagination ' . $class . '"' . (!empty ($id) ? ' id="' . $id . '"' : '') . '>';

		} elseif ($frontend_kit == 'uikit') {
			$output .= '
			<ul class="uk-pagination ' . $class . '"' . (!empty ($id) ? ' id="' . $id . '"' : '') . '>';

		} else {
			$output.= '
			<div class="pagination clearfix ' . $class . '"' . (!empty ($id) ? ' id="' . $id . '"' : '') . '>';

			if ($use_ul) {
				$output.= '<ul>';
			} else {
				if ($show_pages) {
					$add = ($data['pages'] % 100 == 1) ? '' : $pages_plural;
					$output.= '
				<span>' . $data['pages'] . ' stran' . $add . '</span>';
				}
			}
		}




		if ($data['page'] > 1) {
			if ($use_ul)
				$output.= '<li>';
			$output.= '
				<a class="pprev" href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => $prev), null, $include) . $hash . '">' . $page_prev . '</a>';
			if ($use_ul)
				$output.= '</li>';
		} else {
			if (!$use_ul)
			$output.= '
				<span class="pprev empty"></span>';
		}


		//	First page
		if ($start_page > 1) {
			if ($use_ul)
				$output.= '<li>';

			$output.= '
				<a href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => 1)) . $hash . '">1</a>';

			if ($frontend_kit == 'uikit') {
				$output.= '
					</li>
					<li class="uk-disabled"><span>...</span></li>';

			} elseif ($use_ul) {
				$output.= '
					</li>
					<li class="disabled"><a>...</a></li>';
			} else {
				$output.= ' ... ';
			}
		}

		for ($i = $start_page; $i <= $max; $i++) {
			if ($use_ul) {
				if ($i == $data['page']) {
					if ($frontend_kit == 'uikit') {
						$output.= '<li class="uk-active">';
					} else {
						$output.= '<li class="active">';
					}

				} else {
					$output.= '<li>';
				}
			}

			$l = ($i == $max) ? ' l' : '';
			if ($frontend_kit == 'uikit') {
				$output.=  ($i == $data['page']) ? '<span>' . $i . '</span>' . "\n" : '<a href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => $i), null, $include) . $hash . '" class="' . $l . '">' . $i . '</a>' . "\n";
			} elseif ($use_ul) {
				$output.=  ($i == $data['page']) ? '<a href="#">' . $i . '</a>' . "\n" : '<a href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => $i), null, $include) . $hash . '" class="' . $l . '">' . $i . '</a>' . "\n";
			} else {
				$output.=  ($i == $data['page']) ? '<span class="sel' . $l . ' active">' . $i . '</span>' . "\n" : '<a href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => $i), null, $include) . $hash . '" class="' . $l . '">' . $i . '</a>' . "\n";
			}

			if ($use_ul)
				$output.= '</li>';
		}

		//	Last page
		if ($max < $data['pages']) {
			if ($frontend_kit == 'uikit') {
				$output.= '
					<li class="uk-disabled"><span>...</spn></li>
					<li>';

			} elseif ($use_ul) {
				$output.= '
					<li class="disabled"><a>...</a></li>
					<li>';
			} else {
				$output.= ' ... ';
			}

			$output.= '
				<a href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => $data['pages']), null, $include) . $hash . '">' . $data['pages'] . '</a>';
			if ($use_ul)
				$output.= '</li>';
		}

		if ($data['page'] < $max) {
			if ($use_ul)
				$output.= '<li>';
			$output.= '
				<a class="pnext" href="' . Config::get ("page")->getUrl(array ($data['pagevar'] => $next), null, $include) . $hash . '">' . $page_next . '</a>';
			if ($use_ul)
				$output.= '</li>';
		} else {
			$output.= '
				<span class="pnext empty"></span>';
		}

		if ($bootstrap == 3) {
			$output .= '</ul>';

		} elseif ($frontend_kit == 'uikit') {
			$output .= '</ul>';

		} else {
			if ($use_ul)
				$output.= '</ul>';

			$output.= '
			</div>';
		}

	}

	return $output;
}
