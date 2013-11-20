<?php

namespace App\GameModule;

use Framework;


class ConnectionPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param string $id
	 */
	public function actionEditConnection($id)
	{
		$this->getComponent('connection')->connectionId = (string)$id;
	}
}