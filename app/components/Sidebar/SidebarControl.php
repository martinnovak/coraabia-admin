<?php

namespace App;

use Nette,
	Framework;


class SidebarControl extends Framework\Application\UI\BaseControl
{
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/' . strtolower($this->locales->module) . '.latte');
		$template->render();
	}
}
