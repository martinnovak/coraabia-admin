<?php

namespace App\GameModule;

use Framework;


class ImagePresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionEditArtist($id) {
		$this->getComponent('image')->artistId = (int)$id;
	}
}