<?php

namespace Model;

use Nette;



class Game extends Model
{
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getCards()
	{
		$result = $this->connection->selectionFactory->table('card');
		return $this->locales->server == 'dev' ? $result : $result->where('c.ready', TRUE);
	}
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getUserdata()
	{
		return $this->connection->selectionFactory->table('userdata');
	}
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getTranslations()
	{
		return $this->connection->selectionFactory->table('translation');
	}
	
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getPermissions()
	{
		return $this->connection->selectionFactory->table('permission')->select('role_id, resource, server');
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
	
	
	
	/**
	 * @todo TODO
	 * @param GameDeck $gameDeck
	 * @return boolean
	 * @throws Exception 
	 */
	public function setGameDeck(GameDeck $gameDeck)
	{
		try {
			$this->connection->insert('deck', $gameDeck->toArray())->execute();
			$id = $gameDeck->deck_id;
			$this->connection->insert('deck_card', array_map(function ($item) use ($id) {
				return array(
					'deck_id' => $id,
					'card_id' => $item->card_id
				);
			}, $gameDeck->cards))->execute();
		} catch (Exception $e) {
			throw $e;
			return FALSE;
		}
		return TRUE;
	}
}
