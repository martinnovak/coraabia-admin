<?php

namespace App\CoraabiaModule;

use Framework;


class PlayerPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionEditPlayer($id) {
		$this->getComponent('player')->userId = (int)$id;
	}
}