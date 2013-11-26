<?php

namespace App\GameModule;

use Framework;


class TextPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param string $id 
	 */
	public function actionEditGameText($id) {
		$this->getComponent('text')->key = $id;
	}
}