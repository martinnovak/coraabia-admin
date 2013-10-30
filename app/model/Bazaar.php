<?php

namespace Model;


class Bazaar extends Model
{
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function getShopOffers()
	{
		return $this->getSource()->create('FIND_OFFER', 'findOfferResponse.offer')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findOfferFilter', array('type' => array('SHOP')));
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function saveOffer(array $offer)
	{
		return $this->getSource()->create('SAVE_OFFER', 'saveOfferResponse.offerId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveOfferOperation', array('offer' => $offer))
				->load();
	}
}
