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
	 * @return int
	 */
	public function saveOffer(array $offer)
	{
		return $this->getSource()->create('SAVE_OFFER', 'saveOfferResponse.offerId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveOfferOperation', array('offer' => $offer))
				->load();
	}
	
	
	public function deleteOffer($offerId)
	{
		return $this->getSource()->create('DELETE_OFFER', '')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('deleteOfferOperation', array('offerId' => $offerId))
				->load();
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function getShopItems()
	{
		return $this->getSource()->create('FIND_ITEM', 'findItemResponse.item')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findItemFilter', array(
					'includeShopOffers' => TRUE,
					'includeMarketOffers' => FALSE
				));
	}
}
