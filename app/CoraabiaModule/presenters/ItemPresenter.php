<?php

namespace App\CoraabiaModule;

use Framework;


class ItemPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionEditItem($id) {
		$this->getComponent('item')->itemId = (int)$id;
	}
}