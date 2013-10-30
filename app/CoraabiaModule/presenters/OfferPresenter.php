<?php

namespace App\CoraabiaModule;

use Framework;


class OfferPresenter extends Framework\Application\UI\SecuredPresenter
{
	/**
	 * @param int $id 
	 */
	public function actionEditOffer($id) {
		$this->getComponent('offer')->offerId = (int)$id;
	}
}