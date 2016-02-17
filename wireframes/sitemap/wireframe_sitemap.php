<?php

class WireframeSitemap extends WireframeIndex {

	public function build ()
	{
		parent::build();
		header('Content-type: text/xml; charset=utf-8');
	}

}
