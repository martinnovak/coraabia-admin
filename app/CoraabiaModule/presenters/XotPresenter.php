<?php

namespace App\CoraabiaModule;

use Framework;


class XotPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionEditRefill($id) {
		$this->getComponent('xot')->refillId = (int)$id;
	}
}