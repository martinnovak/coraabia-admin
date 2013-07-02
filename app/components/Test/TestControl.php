<?php

namespace App;

use Nette,
	Framework;



class TestControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\API @inject */
	public $api;
	
	
	
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/test.latte');
		
		$result = $this->api->query(array(
			'id' => 'transactions',
			'start' => 0,
			'count' => 50,
			'type' => 'SAVE_USER',
			'userId' => 126,
			'from' => $this->api->formatDate('2010-09-10 12:43:42'),
			'to' => $this->api->formatDate('2010-09-10T14:43:42')
		));
		
		$template->test = json_encode($result);
		
		$template->render();
	}
}
