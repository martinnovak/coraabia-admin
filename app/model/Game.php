<?php

namespace Model;

use Nette;


class Game extends Model
{
	/**
	 * @return \DibiFluent
	 */
	public function getCards()
	{
		$result = $this->connection
				->select('c.*, t.value AS translated_name')
				->from('card c')
				->leftJoin('translation t')
				->on('t.key = %s || CAST(c.card_id AS TEXT)', 'card.')
				->where('t.lang = %s', $this->locales->lang);
		return $this->locales->server == 'dev' ? $result : $result->and('c.ready = %b', TRUE);
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getUserdata()
	{
		return $this->connection
				->select('*')
				->from('userdata');
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getTranslations()
	{
		return $this->connection
				->select('*')
				->from('translation');
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getPermissions()
	{
		return $this->connection
				->select('role_id, resource, server')
				->from('permission');
	}
	
	
	
	/**
	 * @return \DibiFluent
	 */
	public function getGamerooms()
	{
		$result = $this->connection
				->select('g.*, t.value AS translated_name')
				->from('gameroom g')
				->leftJoin('translation t')
				->on('t.key = %s || CAST(g.gameroom_id AS TEXT)', 'gameroom.')
				->where('t.lang = %s', $this->locales->lang);
		return $this->locales->server == 'dev' ? $result : $result->and('g.ready = %b', TRUE);
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
	 * @param \Model\GameDeck $gameDeck
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
