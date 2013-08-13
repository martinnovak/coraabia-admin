<?php

namespace App\GameModule;

use Nette,
	Framework,
	Model,
	Grido;


class TestControl extends Framework\Application\UI\BaseControl
{
	/** @var \Framework\Mapi\MapiRequestFactory @inject */
	public $mapiRequestFactory;
	
	
	public function renderDefault()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/test.latte');
		
		
		/*$request = $this->mapiRequestFactory->create('mass-user-save', 'status');
		$params = $this->getPresenter()->getContext()->getParameters();
		$file = $params['gameXmlDir'] . '/khopts.txt';
		$data = file($file, FILE_IGNORE_NEW_LINES);
		$myLine = '';
		try {
			foreach ($data as $line) {
				$myLine = $line;
				list($user_id, $khopts) = explode(';', $line);
				$request->setParam('userId', (int)$user_id);
				$request->setParam('addXot', (int)$khopts);
				$request->setParam('addTrin', 0);
				$request->setParam('reason', 'OPERATOR');

				Nette\Diagnostics\Debugger::dump($request);
				
				$request->load();
			}
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($myLine);
			$this->getPresenter()->flashMessage($e->getMessage());
		}*/
		
		
		$template->render();
	}
}
