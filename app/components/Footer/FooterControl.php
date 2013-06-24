<?php

namespace App;

use Nette,
	Framework;



class FooterControl extends Framework\Application\UI\BaseControl
{
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/footer.latte');
		$template->now = time();
		$template->render();
	}
}