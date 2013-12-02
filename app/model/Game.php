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
	 * @return array
	 */
	public function getArtists()
	{
		return $this->getDatasource()->getArtists();
	}
	
	
	/**
	 * @return array
	 */
	public function getArtistById($artistId)
	{
		foreach ($this->getArtists() as $artist) {
			if ($artist->artist_id == (int)$artistId) {
				return $artist;
			}
		}
	}
	
	
	public function deleteArtist($artistId)
	{
		return $this->getDatasource()->deleteArtist((int)$artistId);
	}
	
	
	/**
	 * @return array
	 */
	public function getCountries()
	{
		return $this->getDatasource()->getCountries();
	}
	
	
	public function getCountriesAsSelect()
	{
		$countries = array();
		foreach ($this->getCountries() as $country) {
			$countries[substr($country->key, -2)] = $country->value;
		}
		return $countries;
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
	 * @return array
	 */
	public function getActivities()
	{
		return $this->getDatasource()->getActivities();
	}
	
	
	public function getActivityById($activityId)
	{
		foreach ($this->getActivities() as $activity) {
			if ($activity->activity_id == $activityId) {
				return $activity;
			}
		}
	}
	
	
	public function getActivityByVersionId($versionId)
	{
		return $this->getDatasource()->getActivityByVersionId($versionId);
	}
	
	
	public function getFilterByVersionId($versionId)
	{
		return $this->getDatasource()->getFilterByVersionId($versionId);
	}
	
	
	/**
	 * @return array
	 */
	public function getFractions()
	{
		return array(
			'GUARDIAN' => 'Strážce',
			'XENNO' => 'Xeňan',
			'MERCENARY' => 'Žoldák',
			'UNLIVING' => 'Neživý',
			'OUTLAW' => 'Padouch'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getRarities()
	{
		return array(
			'C' => 'Common',
			'U' => 'Uncommon',
			'R' => 'Rare',
			'G' => 'Guru'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityVariantTypes()
	{
		return array(
			'ACTIVITY' => 'Aktivita',
			'TITLE' => 'Titul',
			'GRIND' => 'Grind'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityActivityTypes()
	{
		return array(
			'NEW' => 'Nová',
			'SPECIAL' => 'Speciální',
			'TOURNAMENT' => 'Turnaj',
			'CLASSIC' => 'Klasická'
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityStartTypes()
	{
		return array(
			'MM' => 'MM',
			'PVP' => 'PVP',
			'ELO' => 'ELO'
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
	 * @return array
	 */
	public function getGamerooms()
	{
		return $this->getDatasource()->getGamerooms();
	}
	
	
	public function getGameroomById($gameroomId)
	{
		foreach ($this->getGamerooms() as $gameroom) {
			if ($gameroom->gameroom_id == $gameroomId) {
				return $gameroom;
			}
		}
	}
	
	
	public function getObserverByVersionId($versionId)
	{
		return $this->getDatasource()->getObserverByVersionId($versionId);
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
	 * @return array
	 */
	public function getBots()
	{
		return $this->getDatasource()->getBots();
	}
	
	
	public function getBotsAsSelect()
	{
		$bots = array();
		foreach ($this->getBots() as $bot) {
			$bots[$bot->bot_id] = $bot->name;
		}
		return $bots;
	}
	
	
	/**
	 * @return array
	 */
	public function getActivityRewardTypes()
	{
		return array(
			'CONNECTION' => 'Konexe',
			'TRIN' => 'Triny',
			'CARD' => 'Karta',
			'EXP' => 'Exp',
			'XOT' => 'Xot',
			'AVATAR' => 'Avatar'
		);
	}
	
	
	public function createActivity($values)
	{
		try {
			$this->getDatasource()->beginTransaction();
			
			//local variable
			$variableId = $this->saveLocalPlayableVariableForActivity($values->activity_id);
			
			//global variable
			if (!empty($values->global_var)) {
				$this->getDatasource()->saveGlobalVariable($values->global_var, 0, '');
			}
			
			//filter
			$values = (object)array_merge((array)$values, array(
				'filter_id' => $this->getDatasource()->getNewFilterId(),
				'variable_id' => $variableId
			));
			$this->getDatasource()->saveFilter($values);
			$values = (object)array_merge((array)$values, array(
				'filter_version_playable_id' => $this->getDatasource()->getFilterVersionId($values->filter_id, $this->locales->server)->filter_version_id
			));
			
			//observer
			$values = (object)array_merge((array)$values, array(
				'observer_id' => $this->getDatasource()->getNewObserverId()
			));
			$this->getDatasource()->saveObserver($values);
			$values = (object)array_merge((array)$values, array(
				'observer_version_id' => $this->getDatasource()->getObserverVersionId($values->observer_id, $this->locales->server)->observer_version_id
			));
			
			//gameroom
			$values = (object)array_merge((array)$values, array(
				'gameroom_version_id' => $this->getDatasource()->getGameroomVersionId($values->gameroom_id, $this->locales->server)->gameroom_version_id
			));
			
			//parent activity
			$values = (object)array_merge((array)$values, array(
				'parent_version_id' => empty($values->parent_id) ? NULL : $this->getDatasource()->getActivityVersionId($values->parent_id, $this->locales->server)->activity_version_id
			));
			
			//activity
			$this->getDatasource()->saveActivity($values);
			
			$this->getDatasource()->commit();
		} catch (\Exception $e) {
			$this->getDatasource()->rollBack();
			throw $e;
		}
		return $values;
	}
	
	
	public function updateActivity($values)
	{
		try {
			$this->getDatasource()->beginTransaction();
			
			//old values
			$activity = $this->getActivityById($values->activity_id);
			
			//local variable
			$variableId = $this->saveLocalPlayableVariableForActivity($values->activity_id);
			
			//global variable
			if (!empty($values->global_var)) {
				$this->getDatasource()->saveGlobalVariable($values->global_var, 0, '');
			}
			
			//filter
			$values = (object)array_merge((array)$values, array(
				'filter_id' => $this->getDatasource()->getFilterByVersionId($activity->filter_version_playable_id)->filter_id,
				'variable_id' => $variableId
			));
			$this->getDatasource()->saveFilter($values);
			$values = (object)array_merge((array)$values, array(
				'filter_version_playable_id' => $this->getDatasource()->getFilterVersionId($values->filter_id, $this->locales->server)->filter_version_id
			));
			
			//observer
			$values = (object)array_merge((array)$values, array(
				'observer_id' => $this->getDatasource()->getObserverByVersionId($activity->observer_version_id)->observer_id
			));
			$this->getDatasource()->saveObserver($values);
			$values = (object)array_merge((array)$values, array(
				'observer_version_id' => $this->getDatasource()->getObserverVersionId($values->observer_id, $this->locales->server)->observer_version_id
			));
			
			//gameroom
			$values = (object)array_merge((array)$values, array(
				'gameroom_version_id' => $this->getDatasource()->getGameroomVersionId($values->gameroom_id, $this->locales->server)->gameroom_version_id
			));
			
			//parent activity
			$values = (object)array_merge((array)$values, array(
				'parent_version_id' => empty($values->parent_id) ? NULL : $this->getDatasource()->getActivityVersionId($values->parent_id, $this->locales->server)->activity_version_id
			));
			
			//activity
			$this->getDatasource()->saveActivity($values);
			
			$this->getDatasource()->commit();
		} catch (\Exception $e) {
			$this->getDatasource()->rollBack();
			throw $e;
		}
		return $values;
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
	
	
	public function saveConnection($connection)
	{
		try {
			$this->getDatasource()->beginTransaction();
			$this->getDatasource()->saveConnection($connection);
			$this->getDatasource()->commit();
		} catch (\Exception $e) {
			$this->getDatasource()->rollBack();
			throw $e;
		}
		return $connection;
	}
	
	
	public function readyConnection($connectionId, $server)
	{
		try {
			$this->getDatasource()->beginTransaction();
			$version = $this->getConnectionById($connectionId)->version;
			$this->getDatasource()->readyConnection($connectionId, $version, $server);
			$this->getDatasource()->commit();
		} catch (\Exception $e) {
			$this->getDatasource()->rollBack();
			throw $e;
		}
	}
	
	
	public function getAllConnectionsVersions()
	{
		$versions = array();
		foreach ($this->getDatasource()->getAllConnectionsVersions() as $data) {
			$versions[$data->connection_id][$data->server] = $data->version;
		}
		return $versions;
	}
	
	
	public function getAllGameroomsVersions()
	{
		$versions = array();
		foreach ($this->getDatasource()->getAllGameroomsVersions() as $data) {
			$versions[$data->gameroom_id][$data->server] = $data->version;
		}
		return $versions;
	}
	
	
	public function getGameroomByVersionId($versionId)
	{
		return $this->getDatasource()->getGameroomByVersionId($versionId);
	}
	
	
	public function deleteGameroom($gameroomId, $server = NULL)
	{
		if (!$server) {
			$server = $this->locales->server;
		}
		$this->getDatasource()->deleteGameroom($gameroomId, $server);
	}
	
	
	public function saveGameroom($gameroom)
	{
		try {
			$this->getDatasource()->beginTransaction();
			$this->getDatasource()->saveGameroom($gameroom);
			$this->getDatasource()->commit();
		} catch (\Exception $e) {
			$this->getDatasource()->rollBack();
			throw $e;
		}
		return $gameroom;
	}
	
	
	public function readyGameroom($gameroomId, $server)
	{
		try {
			$this->getDatasource()->beginTransaction();
			$version = $this->getGameroomById($gameroomId)->version;
			$this->getDatasource()->readyGameroom($gameroomId, $version, $server);
			$this->getDatasource()->commit();
		} catch (\Exception $e) {
			$this->getDatasource()->rollBack();
			throw $e;
		}
	}
	
	
	protected function saveLocalPlayableVariableForActivity($activityId)
	{
		$variableId = substr($activityId . '_PL', -20);
		$this->getDatasource()->saveLocalVariable($variableId, 0, $activityId . ' playable');
		return $variableId;
	}
	
	
	public function deleteActivity($activityId, $server = NULL)
	{
		if (!$server) {
			$server = $this->locales->server;
		}
		$this->getDatasource()->deleteActivity($activityId, $server);
	}
	
	
	public function getAllActivitiesVersions()
	{
		$versions = array();
		foreach ($this->getDatasource()->getAllActivitiesVersions() as $data) {
			$versions[$data->activity_id][$data->server] = $data->version;
		}
		return $versions;
	}
}
