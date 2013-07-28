<?php

namespace App;

use Nette,
	Framework;



class NewsPresenter extends Framework\Application\UI\SecuredPresenter
{

	public function actionUpdateNews($id) {
		$this->getComponent('news')->newsId = $id;
	}
}