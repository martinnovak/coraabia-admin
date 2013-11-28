<?php

namespace App;

use Framework;


class UserControl extends Framework\Application\UI\BaseControl
{
	
	public function renderProfile()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/profile.latte');
		$template->user = $this->getPresenter()->getUser();
		$template->render();
	}
}
