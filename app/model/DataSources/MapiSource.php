<?php

namespace Model\DataSources;

use Nette,
	Framework,
	Model;


class MapiSource extends Nette\Object implements ISource
{
	/** @var \Framework\Mapi\MapiRequestFactory */
	private $mapiRequestFactory;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Framework\Mapi\MapiRequestFactory $mapiRequestFactory
	 * @param \Model\Locales
	 */
	public function __construct(Framework\Mapi\MapiRequestFactory $mapiRequestFactory, Model\Locales $locales)
	{
		$this->mapiRequestFactory = $mapiRequestFactory;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequestFactory
	 */
	public function getSource()
	{
		return $this->mapiRequestFactory;
	}
	
	
	/**
	 * @return array
	 */
	public function getTransactions()
	{
		return $this->mapiRequestFactory->create('FIND_TRANSACTION', 'findTransactionResponse.transactions')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findTransactionFilter', array('types' => array()))
				->load();
	}
	
	
	/**
	 * @return array
	 */
	public function getShopItems()
	{
		return $this->mapiRequestFactory->create('FIND_ITEM', 'findItemResponse.item')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findItemFilter', array(
					'includeShopOffers' => TRUE,
					'includeMarketOffers' => FALSE
				))
				->load();
	}
	
	
	/**
	 * @param array $item
	 * @return int
	 */
	public function saveShopItem(array $item)
	{
		return $this->mapiRequestFactory->create('SAVE_ITEM', 'saveItemResponse.itemId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveItemOperation', array('item' => array($item)))
				->load();
	}
	
	
	/**
	 * @return array
	 */
	public function getShopOffers()
	{
		return $this->mapiRequestFactory->create('FIND_OFFER', 'findOfferResponse.offer')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findOfferFilter', array('type' => array('SHOP')))
				->load();
	}
	
	
	/**
	 * @param int $offerId
	 */
	public function deleteOffer($offerId)
	{
		return $this->mapiRequestFactory->create('DELETE_OFFER', '')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('deleteOfferOperation', array('offerId' => (int)$offerId))
				->load();
	}
	
	
	/**
	 * @param array $offer
	 * @return int
	 */
	public function saveOffer(array $offer)
	{
		return $this->mapiRequestFactory->create('SAVE_OFFER', 'saveOfferResponse.offerId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveOfferOperation', array('offer' => $offer))
				->load();
	}
	
	
	/**
	 * @return array
	 */
	public function getPlayers()
	{
		return $this->mapiRequestFactory->create('FIND_USER', 'findUserResponse.user')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findUserFilter', array(
					'includePayments' => TRUE,
					'includeInstances' => TRUE,
					'includeOffers' => TRUE
				))
				->load();
	}
	
	
	public function playerAddCurrency($userId, $amount, $currency, $reason = '')
	{
		return $this->mapiRequestFactory->create('REWARD_USER', 'rewardUserResponse.totalAmount')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('rewardUserOperation', array(
					'userId' => array((int)$userId),
					'amount' => array('currency' => $currency, 'amount' => (int)$amount),
					'reasonCode' => $reason
				))
				->load();
	}

	
	/**
	 * @return array
	 */
	public function getRefills()
	{
		return $this->mapiRequestFactory->create('FIND_REFILL', 'findRefillResponse.refill')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findRefillFilter', new \stdClass())
				->load();
	}
	
	
	/**
	 * @param int $refillId
	 */
	public function deleteRefill($refillId)
	{
		return $this->mapiRequestFactory->create('DELETE_REFILL', '')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('deleteRefillOperation', array('refillId' => (int)$refillId))
				->load();
	}
	
	
	/**
	 * @param array $refill
	 * @return int
	 */
	public function saveRefill(array $refill)
	{
		return $this->mapiRequestFactory->create('SAVE_REFILL', 'saveRefillResponse.refillId')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('saveRefillOperation', array('refill' => $refill))
				->load();
	}
}
