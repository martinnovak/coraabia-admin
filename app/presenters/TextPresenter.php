<?php

namespace App;

use Nette,
	Framework;



class TextPresenter extends Framework\Application\UI\SecuredPresenter
{

	public function actionUpdateStatic($id) {
		$this->getComponent('text')->key = $id;
	}
}