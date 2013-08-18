<?php

namespace App\CoraabiaModule;

use Framework;


class XotPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionDoBazaarEditRefill($id) {
		$this->getComponent('xot')->id = $id;
	}
}