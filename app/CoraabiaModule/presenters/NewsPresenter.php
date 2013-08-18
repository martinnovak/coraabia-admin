<?php

namespace App\CoraabiaModule;

use Framework;


class NewsPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionUpdateNews($id) {
		$this->getComponent('news')->newsId = (int)$id;
	}
}
