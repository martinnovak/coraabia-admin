<?php

namespace App;

use Nette,
	Framework;



class MenuControl extends Framework\Application\UI\BaseControl
{
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/menu.latte');
		$template->user = $this->getPresenter()->getUser();
		$template->render();
	}
}
