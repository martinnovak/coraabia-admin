<?php

namespace Model;

use Framework;


class Game extends Model
{
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getCards()
	{
		return $this->getSource()->getSelectionFactory()->table('card');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getTranslations()
	{
		return $this->getSource()->getSelectionFactory()->table('translation');
	}
	
	
	/**
	 * @param array $deck
	 * @param array $cards
	 * @param array $connections
	 */
	public function createBotDeck(array $deck, array $cards, array $connections)
	{
		$deckId = 'DECK_' . $deck['deck_id'];
		
		$gameDeck = array(
			'deck_id' => $deckId,
			'start_tr' => '',
			'start_ch' => '',
			'type' => 'BOT_' . substr($deck['username'], 3)
		);
		$this->getSource()->query('INSERT INTO deck', $gameDeck);
		
		$cards = array_values(array_map(function ($item) use ($deckId) {
			return array('deck_id' => $deckId, 'card_id' => $item->card_id);
		}, $cards));
		if (count($cards)) {
			$this->getSource()->getSelectionFactory()->table('deck_card')
					->insert($cards);
		}
		
		$connections = array_values(array_map(function ($item) use ($deckId) {
			return array('connection_id' => $item->connection_id, 'deck_id' => $deckId);
		}, $connections));
		if (count($connections)) {
			$this->getSource()->getSelectionFactory()->table('deck_connection')
					->insert($connections);
		}
	}
	
	
	public function deleteBotDecks()
	{
		$this->getSource()->getSelectionFactory()->table('deck')
				->where('type ~ ?', '^BOT_[1-9][0-9]*$')
				->delete();
	}
	
	
	/**
	 * @return array
	 */
	public function getGameTexts()
	{
		return $this->getDatasource()->getGameTexts();
	}
	
	
	public function updateGameTexts(array $texts)
	{
		return $this->getDatasource()->updateGameTexts($texts);
	}
	
	
	public function createGameTexts(array $texts)
	{
		return $this->getDatasource()->createGameTexts($texts);
	}
	
	
	public function deleteGameText($key)
	{
		return $this->getDatasource()->deleteGameText($key);
	}


	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getEditions()
	{
		return $this->getSource()->getSelectionFactory()->table('edition');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getArtists()
	{
		return $this->getSource()->getSelectionFactory()->table('artist')
				->select('artist.*, COUNT(:art.art_id) AS arts')
				->group('artist.artist_id');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getCountries()
	{
		return $this->getSource()->getSelectionFactory()->table('translation')
				->where('key LIKE ?', 'country.%')
				->where('lang = ?', $this->locales->lang);
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getArts()
	{
		return $this->getSource()->getSelectionFactory()->table('art')
				->select('art.*, image.path AS art_path, face.path AS face_path, avatar.path AS avatar_path');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivities()
	{
		return $this->getDatasource()->getActivities();
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getFilters()
	{
		return $this->getSource()->getSelectionFactory()->table('filter');
	}
	
	
	/**
	 * @return array
	 */
	public function getFractions()
	{
		return array(
			'GUARDIAN',
			'XENNO',
			'MERCENARY',
			'UNLIVING',
			'OUTLAW'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityVariants()
	{
		return array(
			'ACTIVITY',
			'TITLE',
			'GRIND'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityTypes()
	{
		return array(
			'CLASSIC',
			'SPECIAL',
			'TOURNAMENT'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityStartTypes()
	{
		return array(
			'MM',
			'PVP',
			'ELO'
		);
	}
		
	
	/**
	 * @param string $table
	 * @param mixed $activityId
	 * @param array $values
	 * @return \Nette\Database\Table\ActiveRow|NULL
	 */
	public function update($table, $id, array $values)
	{
		if ($id !== NULL) { //update
			$this->getSource()->getSelectionFactory()->table($table)
					->where($table . '_id = ?', $id)
					->fetch()
					->update($values);
		} else { //insert
			return $this->getSource()->getSelectionFactory()->table($table)
					->insert($values);
		}
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getGamerooms()
	{
		return $this->getSource()->getSelectionFactory()->table('gameroom');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getObservers()
	{
		return $this->getSource()->getSelectionFactory()->table('observer');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivityGamerooms()
	{
		return $this->getSource()->getSelectionFactory()->table('activity_gameroom');
	}
	
	
	/**
	 * @todo optimize
	 * @param string $activityId
	 * @return \Nette\Database\Table\Selection
	 */
	public function getParentActivities($activityId)
	{
		$obj = Framework\Kapafaa\ObjectFactory::getActivityPlayableSetter($activityId);
		
		//All observers that set my playable variable to TRUE.
		$observers = $this->getObservers()
				->select('observer_id')
				->where('effect_data LIKE ?', "%$obj%");
		return $this->getActivities()
				->where(':activity_observer.observer_id IN ?', $observers);
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getBots()
	{
		return $this->getSource()->getSelectionFactory()->table('bot');
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityRewardTypes()
	{
		return array(
			'CONNECTION',
			'TRIN',
			'CARD',
			'EXP',
			'XOT',
			'AVATAR'
		);
	}
	
	
	/**
	 * @param array $values
	 * @return \Nette\Database\ResultSet|NULL
	 */
	public function createActivity(array $values)
	{
		return $this->getSource()->query('INSERT INTO activity', array(
						'activity_id' => $values['activity_id'],
						'fraction' => $values['fraction'] ?: NULL,
						'posx' => (int)$values['posx'],
						'posy' => (int)$values['posy'],
						'authority' => $values['authority'],
						'art_id' => $values['art_id'] ? (int)$values['art_id'] : NULL,
						'bot_id' => $values['bot_id'] ? (int)$values['bot_id'] : NULL,
						'variant_type' => $values['variant_type'],
						'activity_type' => $values['activity_type'],
						'start_type' => $values['start_type'],
						'reward_type' => $values['reward_type'] ?: NULL,
						'reward_value' => $values['reward_type'] && $values['reward_value'] ? $values['reward_value'] : NULL,
						'tree' => (int)$values['tree'],
						'ready' => FALSE
					));
	}
	
	
	/**
	 * @param array $values
	 * @return \Nette\Database\Table\Selection
	 */
	public function createFilter(array $values)
	{
		return $this->getSource()->getSelectionFactory()->table('filter')
				->insert($values);
	}
	
	
	/**
	 * @param array $values
	 * @return \Nette\Database\Table\Selection
	 */
	public function createObserver(array $values)
	{
		return $this->getSource()->getSelectionFactory()->table('observer')
				->insert($values);
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivityObservers()
	{
		return $this->getSource()->getSelectionFactory()->table('activity_observer');
	}
	
	
	/**
	 * @return \Nette\Database\Table\Selection
	 */
	public function getActivityPlayableFilters()
	{
		return $this->getSource()->getSelectionFactory()->table('activity_filter_playable');
	}
	
	
	/**
	 * @return array
	 */
	public function getConnections()
	{
		return $this->getDatasource()->getConnections();
	}
	
	
	/**
	 * @return array
	 */
	public function getConnectionById($connectionId)
	{
		foreach ($this->getConnections() as $connection) {
			if ($connection->connection_id == $connectionId) {
				return $connection;
			}
		};
	}
	
	
	public function deleteConnection($connectionId, $server = NULL)
	{
		if (!$server) {
			$server = $this->locales->server;
		}
		$this->getDatasource()->deleteConnection($connectionId, $server);
	}
	
	
	public function getConnectionTypes()
	{
		return array(
			'MULTIVERSE',
			'GAME'
		);
	}
	
	
	public function saveConnection(array $connection, $server = NULL)
	{
		if (!$server) {
			$server = $this->locales->server;
		}
		$this->getDatasource()->createConnection($connection, $server);
		return $connection['connection_id'];
	}
	
	
	public function readyConnection($connectionId, $server)
	{
		$version = $this->getConnectionById($connectionId)->version;
		return $this->getDatasource()->readyConnection($connectionId, $version, $server);
	}
	
	
	public function getConnectionVersions()
	{
		$versions = array();
		foreach ($this->getDatasource()->getConnectionVersions() as $data) {
			$versions[$data->connection_id][$data->server] = $data->version;
		}
		return $versions;
	}
}
