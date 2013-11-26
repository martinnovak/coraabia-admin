<?php

namespace Model;


class Bazaar extends Model
{
	/**
	 * @return array
	 */
	public function getShopOffers()
	{
		return $this->getDatasource()->getShopOffers();
	}
	
	
	public function getShopOfferById($offerId)
	{
		foreach ($this->getShopOffers() as $offer) {
			if ($offer->offerId == (int)$offerId) {
				return $offer;
			}
		}
	}
	
	
	/**
	 * @param array $offer
	 * @return int
	 */
	public function saveOffer(array $offer)
	{
		return $this->getDatasource()->saveOffer($offer);
	}
	
	
	/**
	 * @param int $offerId
	 */
	public function deleteOffer($offerId)
	{
		return $this->getDatasource()->deleteOffer($offerId);
	}
	
	
	/**
	 * @return array
	 */
	public function getShopItems()
	{
		return $this->getDatasource()->getShopItems();
	}
	
	
	/**
	 * @param int $itemId
	 * @return object
	 */
	public function getShopItemById($itemId)
	{
		foreach ($this->getShopItems() as $item) {
			if ($item->itemId == (int)$itemId) {
				return $item;
			}
		}
	}
	
	
	/**
	 * @return array
	 */
	public function getRefills()
	{
		return $this->getDatasource()->getRefills();
	}
	
	
	public function getRefillById($refillId)
	{
		foreach ($this->getRefills() as $refill) {
			if ($refill->refillId == (int)$refillId) {
				return $refill;
			}
		}
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
		return $this->getDatasource()->saveRefill($refill);
	}
	
	
	/**
	 * @param int $refillId
	 */
	public function deleteRefill($refillId)
	{
		return $this->getDatasource()->deleteRefill((int)$refillId);
	}
	
	
	/**
	 * @param array $item
	 * @return int
	 */
	public function saveShopItem(array $item)
	{
		return $this->getDatasource()->saveShopItem($item);
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
	 * @return \Framework\Utils\SmartObject|NULL
	 */
	public function getTransactionById($id)
	{
		foreach ($this->getTransactions() as $transaction) {
			if ($transaction->txId == (int)$id) {
				return $transaction;
			}
		}
	}
	
	
	/**
	 * @return array
	 */
	public function getPlayers()
	{
		return $this->getDatasource()->getPlayers();
	}
	
	
	public function getPlayerById($userId)
	{
		foreach ($this->getPlayers() as $player) {
			if ($player->userId == (int)$userId) {
				return $player;
			}
		}
	}
	
	
	public function playerAddTrin($userId, $amount, $reason = '')
	{
		$this->getDatasource()->playerAddCurrency((int)$userId, (int)$amount, 'TRI', $reason);
	}
	
	
	public function playerAddXot($userId, $amount, $reason = '')
	{
		$this->getDatasource()->playerAddCurrency((int)$userId, (int)$amount, 'XOT', $reason);
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
