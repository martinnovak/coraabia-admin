<?php

namespace Model;


class Bazaar extends Model
{
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function getShopOffers()
	{
		return $this->getSource()->create('FIND_OFFER', 'findOfferResponse')
				->setParam('timestamp', time())
				->setParam('counter', 0)
				->setParam('findOfferFilter', array('type' => array('SHOP')));
	}
}
