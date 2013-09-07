<?php

namespace App\GameModule;

use Framework;


class ActivityPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param string $id 
	 */
	public function actionEditActivity($id) {
		$this->getComponent('activity')->activityId = (string)$id;
	}
}