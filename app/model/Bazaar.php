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
	 * @param array $offer
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
	
	
	/**
	 * @param int $offerId
	 */
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
	
	
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function getRefills()
	{
		return $this->getSource()->create('FIND_REFILL', 'findRefillResponse.refill')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findRefillFilter', new \stdClass());
	}
	
	
	/**
	 * @return array
	 */
	public function getCurrencies()
	{
		return array(
			'XOT' => 'Xot',
			'TRI' => 'Trin'
		);
	}
	
	
	/**
	 * @param array $refill
	 * @return int
	 */
	public function saveRefill(array $refill)
	{
		return $this->getSource()->create('SAVE_REFILL', 'saveRefillResponse.refillId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveRefillOperation', array('refill' => $refill))
				->load();
	}
	
	
	/**
	 * @param int $refillId
	 */
	public function deleteRefill($refillId)
	{
		return $this->getSource()->create('DELETE_REFILL', '')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('deleteRefillOperation', array('refillId' => $refillId))
				->load();
	}
	
	
	/**
	 * @param array $item
	 * @return int
	 */
	public function saveItem(array $item)
	{
		return $this->getSource()->create('SAVE_ITEM', 'saveItemResponse.itemId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveItemOperation', array('item' => array($item)))
				->load();
	}
	
	
	/**
	 * @return array
	 */
	public function getTransactions()
	{
		return $this->getDatasource()->getTransactions();
	}
	
	
	/**
	 * @param int $id
	 * @return \Framework\Mapi\MapiObject|NULL
	 */
	public function getTransactionById($id)
	{
		foreach ($this->getDatasource()->getTransactions() as $transaction) {
			if ($transaction->txId == (int)$id) {
				return $transaction;
			}
		}
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function getPlayers()
	{
		return $this->getSource()->create('FIND_USER', 'findUserResponse.user')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findUserFilter', array(
					'includePayments' => TRUE,
					'includeInstances' => TRUE,
					'includeOffers' => TRUE
				));
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequest
	 */
	public function rewardPlayer()
	{
		return $this->getSource()->create('REWARD_USER', 'rewardUserResponse.totalAmount')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('rewardUserOperation', new \stdClass());
	}
	
	
	/**
	 * @return array
	 */
	public function getBazaarTransactionTypes()
	{
		return array(
			'USER_CREATED',
			'REWARD_TRIN',
			'REWARD_XOT',
			'REWARD_XOT_INVITED',
			'REWARD_ITEM',
			'BUY_XOT',
			'CHANGE_OFFER',
			'SAVE_ITEM',
			'BUY_ITEM',
			'BUY_INSTANCE',
			'SELL_TO_IBLORT',
			'INIT_EXTERNAL_PAYMENT',
			'FINISH_EXTERNAL_PAYMENT',
			'CANCEL_EXTERNAL_PAYMENT',
			'IMPORT_CARD',
			'UPDATE_CARD',
			'ASSIGN_PAYMENT_TX',
			'PAYMILL_REQUEST',
			'PAYPAL_REQUEST',
			'FORTUMO_NEW_PRICE',
			'FORTUMO_UPDATE_PRICE',
			'FORTUMO_REQUEST'
		);
	}
}
