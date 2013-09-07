<?php

namespace App;

use Framework;


class MenuControl extends Framework\Application\UI\BaseControl
{
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/menu.latte');
		$template->user = $this->getPresenter()->getUser();
		
		$test = $this->getPresenter()->link(':Coraabia:User:profile', array('server' => 'dev'));
		
		$template->render();
	}
}
