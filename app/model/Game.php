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
	
	
	
	public function createBotDeck(array $deck, array $cards, array $connections)
	{
		$deckId = 'DECK_' . $deck['deck_id'];
		
		$gameDeck = array(
			'deck_id' => $deckId,
			'start_tr' => '',
			'start_ch' => '',
			'type' => 'BOT_' . substr($deck['username'], 3)
		);
		$this->connection->query('INSERT INTO deck', $gameDeck);
		
		$cards = array_values(array_map(function ($item) use ($deckId) {
			return array('deck_id' => $deckId, 'card_id' => $item->card_id);
		}, $cards));
		if (count($cards)) {
			$this->connection->selectionFactory->table('deck_card')->insert($cards);
		}
		
		$connections = array_values(array_map(function ($item) use ($deckId) {
			return array('connection_id' => $item->connection_id, 'deck_id' => $deckId);
		}, $connections));
		if (count($connections)) {
			$this->connection->selectionFactory->table('deck_connection')->insert($connections);
		}
	}
	
	
	
	public function deleteBotDecks()
	{
		$this->connection->table('deck')->where('type ~ ?', '^BOT_[1-9][0-9]*$')->delete();
	}
}
