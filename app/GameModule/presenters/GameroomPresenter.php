<?php

namespace App\GameModule;

use Framework;


class GameroomPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param string $id
	 */
	public function actionEditGameroom($id)
	{
		$this->getComponent('gameroom')->gameroomId = (string)$id;
	}
}