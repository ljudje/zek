<?php

class WireframeHome extends WireframeIndex {

	public function build ()
	{
		$this->assign ('home', true);

		parent::build();
	}

}
