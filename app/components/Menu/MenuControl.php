<?php

namespace App;

use Nette,
	Framework;



class MenuControl extends Framework\Application\UI\BaseControl
{
	/** @var \Nette\Security\User @inject */
	public $user;
	
	
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/menu.latte');
		$template->user = $this->user;
		$template->render();
	}
}
