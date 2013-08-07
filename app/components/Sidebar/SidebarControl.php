<?php

namespace App;

use Nette,
	Framework;


class SidebarControl extends Framework\Application\UI\BaseControl
{
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/sidebar.latte');
		$template->render();
	}
}
