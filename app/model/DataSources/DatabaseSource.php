<?php

namespace Model\DataSources;

use Nette,
	Model;


class DatabaseSource extends Nette\Object implements ISource
{
	/** @var \Nette\Database\Connection */
	private $connection;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Nette\Database\Connection $connection
	 */
	public function __construct(Nette\Database\Connection $connection, Model\Locales $locales)
	{
		$this->connection = $connection;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Nette\Database\Connection
	 */
	public function getSource()
	{
		return $this->connection;
	}
	
	
	public function beginTransaction()
	{
		$this->connection->beginTransaction();
	}
	
	
	public function commit()
	{
		$this->connection->commit();
	}
	
	
	public function rollBack()
	{
		$this->connection->rollBack();
	}
	

	/**
	 * @return array
	 */
	public function getUserdata()
	{
		return $this->connection->getSelectionFactory()
				->table('userdata')
				->fetchAll();
	}
	
	
	/**
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->connection->getSelectionFactory()
				->table('permission')
				->fetchAll();
	}
	
	
	/**
	 * @param int $userId
	 * @param array $params
	 */
	public function updateUser($userId, array $params)
	{
		$this->connection->getSelectionFactory()
				->table('userdata')
				->where('user_id = ?', (int)$userId)
				->fetch()
				->update($params);
	}
	
	
	/**
	 * @return array
	 */
	public function getAudits()
	{
		return $this->connection->getSelectionFactory()
				->table('audit_event')
				->fetchAll();
	}
	
	
	/**
	 * @return array
	 */
	public function getDecks()
	{
		return $this->connection->getSelectionFactory()
				->table('deck')
				->select('deck.*, user.user_id, user.username')
				->fetchAll();
	}
	
	
	/**
	 * @return array
	 */
	public function getNews()
	{
		return $this->connection->getSelectionFactory()
				->table('news')
				->select('*, CASE WHEN valid_from IS NOT NULL THEN valid_from ELSE created END AS order_by')
				->fetchAll();
	}
	
	
	/**
	 * @param int $newsId
	 */
	public function deleteNews($newsId)
	{
		return $this->connection->getSelectionFactory()
				->table('news')
				->where('news_id = ?', (int)$newsId)
				->fetch()
				->delete();
	}
	
	
	public function updateNews($newsId, array $values)
	{
		$news = $this->connection->getSelectionFactory()
				->table('news')
				->where('news_id = ?', (int)$newsId)
				->fetch();
		$news->update($values);
		return $news;
	}
	
	
	public function createNews(array $values)
	{
		return $this->connection->getSelectionFactory()
				->table('news')
				->insert($values);
	}
	
	
	/**
	 * @return array
	 */
	public function getPlayers()
	{
		return $this->connection->getSelectionFactory()
				->table('userdata')
				->fetchAll();
	}
	
	
	public function updatePlayer($userId, array $values)
	{
		$player = $this->connection->getSelectionFactory()
				->table('userdata')
				->where('user_id = ?', $userId)
				->fetch();
		$player->update($values);
		return $player;
	}
	
	
	/**
	 * @return array
	 */
	public function getActivities()
	{
		return $this->connection->query(
				'SELECT activity.* FROM activity
				LEFT JOIN activity_version USING (activity_id, version)
				WHERE activity_version.server = ?',
				$this->locales->server)
				->fetchAll();
	}
	
	
	public function getActivityByVersionId($versionId)
	{
		return $this->connection->query(
				'SELECT activity.* FROM activity
				LEFT JOIN activity_version USING (activity_id, version)
				WHERE activity_version.activity_version_id = ?',
				(int)$versionId
				)->fetch();
	}
	
	
	/**
	 * @param string $activityId
	 * @param string $server
	 */
	public function deleteActivity($activityId, $server)
	{
		$this->connection->query(		
				'DELETE FROM activity_version
				WHERE activity_id = ? AND server = ?',
				$activityId, $server
		);
	}
	
	
	public function getAllActivitiesVersions()
	{
		return $this->connection->getSelectionFactory()
				->table('activity_version')
				->fetchAll();
	}
	
	
	public function saveActivity($values)
	{
		//get new version number
		$version = (int)$this->connection->query(
				'SELECT MAX(version) AS version FROM activity WHERE activity_id = ?',
				$values->activity_id)
				->fetch()
				->version + 1;

		//insert activity
		$this->connection->getSelectionFactory()
				->table('activity')
				->insert(array(
					'activity_id' => $values->activity_id,
					'version' => $version,
					'rarity' => $values->rarity,
					'fraction' => empty($values->fraction) ? NULL : $values->fraction,
					'tree' => (int)$values->tree,
					'posx' => (int)$values->posx,
					'posy' => (int)$values->posy,
					'mentor' => empty($values->mentor) ? NULL : $values->mentor,
					'art_id' => empty($values->art_id) ? NULL : (int)$values->art_id,
					'bot_id' => empty($values->bot_id) ? NULL : (int)$values->bot_id,
					'variant_type' => $values->variant_type,
					'activity_type' => $values->activity_type,
					'start_type' => $values->start_type,
					'reward_type' => empty($values->reward_type) ? NULL : $values->reward_type,
					'reward_value' => empty($values->reward_type) ? NULL : $values->reward_value, //intentional
					'created' => $this->locales->timestamp,
					'server' => $this->locales->server,
					'filter_version_visible_id' => NULL, //@todo
					'filter_version_playable_id' => $values->filter_version_playable_id,
					'observer_version_id' => $values->observer_version_id,
					'gameroom_version_id' => $values->gameroom_version_id,
					'parent_version_id' => $values->parent_version_id
				));

		//insert/update version record
		$this->readyActivity($values->activity_id, (int)$version, $this->locales->server);

		//texts
		$texts = array();
		foreach ($values as $key => $value) {
			if (preg_match('/^(activity_name|activity_flavor|activity_task|activity_finish)_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
				$texts[] = array(
					'key' => str_replace('_', '-', $matches[1]) . '.' . $values->activity_id . '.' . (int)$version,
					'lang' => $matches[2],
					'value' => $value
				);
			}
		}
		$this->connection->getSelectionFactory()
				->table('translation')
				->insert($texts);
	}
	
	
	protected function readyActivity($activityId, $version, $server)
	{
		$this->connection->query(
				'UPDATE activity_version SET version = ? WHERE activity_id = ? AND server = ?',
				(int)$version, $activityId, $server
		);
		$this->connection->query(
				'INSERT INTO activity_version (activity_id, version, server) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM activity_version WHERE activity_id = ? AND server = ?)',
				$activityId, (int)$version, $server, $activityId, $server
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getConnections()
	{
		return $this->connection->query(
				'SELECT connection.* FROM connection
				LEFT JOIN connection_version USING (connection_id, version)
				WHERE connection_version.server = ?',
				$this->locales->server)
				->fetchAll();
	}
	
	
	/**
	 * @param string $connectionId
	 * @param string $server
	 */
	public function deleteConnection($connectionId, $server)
	{
		$this->connection->query(		
				'DELETE FROM connection_version
				WHERE connection_id = ? AND server = ?',
				$connectionId, $server
		);
	}
	
	
	public function saveConnection($connection)
	{
		//get new version number
		$version = (int)$this->connection->query(
				'SELECT MAX(version) AS version FROM connection WHERE connection_id = ?',
				$connection->connection_id)
				->fetch()
				->version + 1;

		//insert connection
		$this->connection->getSelectionFactory()
				->table('connection')
				->insert(array(
					'connection_id' => $connection->connection_id,
					'influence_cost' => (int)$connection->influence_cost,
					'art_id' => empty($connection->art_id) ? NULL : (int)$connection->art_id,
					'type' => $connection->type,
					'effect_data' => $connection->effect_data,
					'version' => (int)$version,
					'created' => $this->locales->timestamp,
					'server' => $this->locales->server
				));

		//insert/update version record
		$this->readyConnection($connection->connection_id, (int)$version, $this->locales->server);

		//texts
		$texts = array();
		foreach ($connection as $key => $value) {
			if (preg_match('/^(connection_name|connection_description|connection_tooltip)_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
				$texts[] = array(
					'key' => str_replace('_', '-', $matches[1]) . '.' . $connection->connection_id . '.' . (int)$version,
					'lang' => $matches[2],
					'value' => $value
				);
			}
		}
		$this->connection->getSelectionFactory()
				->table('translation')
				->insert($texts);
	}
	
	
	public function readyConnection($connectionId, $version, $server)
	{
		$this->connection->query(
				'UPDATE connection_version SET version = ? WHERE connection_id = ? AND server = ?',
				(int)$version, $connectionId, $server
		);
		$this->connection->query(
				'INSERT INTO connection_version (connection_id, version, server) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM connection_version WHERE connection_id = ? AND server = ?)',
				$connectionId, (int)$version, $server, $connectionId, $server
		);
	}
	
	
	protected function getConnectionVersion($connectionId, $server)
	{
		return $this->connection->getSelectionFactory()
				->table('connection_version')
				->where('connection_id = ?', $connectionId)
				->where('server = ?', $server)
				->fetch();
	}
	
	
	public function getAllConnectionsVersions()
	{
		return $this->connection->getSelectionFactory()
				->table('connection_version')
				->fetchAll();
	}
	
	
	/**
	 * @return array
	 */
	public function getGameTexts()
	{
		return $this->connection->getSelectionFactory()
				->table('translation')
				->where('key LIKE ?', \App\GameModule\TextControl::PREFIX . '%')
				->where('lang = ?', $this->locales->lang)
				->fetchAll();
	}
	
	
	public function updateGameTexts(array $texts)
	{
		$this->connection->beginTransaction();
		try {
			foreach ($texts as $text) {
				$this->connection->query(
						'UPDATE translation SET value = ? WHERE key = ? AND lang = ?',
						$text['value'], $text['key'], $text['lang']
				);
			}
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}
	
	
	public function createGameTexts(array $texts)
	{
		return $this->connection->getSelectionFactory()
				->table('translation')
				->insert($texts);
	}
	
	
	public function deleteGameText($key)
	{
		return $this->connection->query('DELETE FROM translation WHERE key = ?', $key);
	}
	
	
	/**
	 * @return array
	 */
	public function getArtists()
	{
		return $this->connection->getSelectionFactory()
				->table('artist')
				->select('artist.*, COUNT(:art.art_id) AS arts')
				->group('artist.artist_id')
				->fetchAll();
	}
	
	
	public function deleteArtist($artistId)
	{
		return $this->connection->query(
				'DELETE FROM artist WHERE artist_id = ?',
				(int)$artistId
		);
	}
	
	
	/**
	 * @return array
	 */
	public function getGamerooms()
	{
		return $this->connection->query(
				'SELECT gameroom.* FROM gameroom
				LEFT JOIN gameroom_version USING (gameroom_id, version)
				WHERE gameroom_version.server = ?',
				$this->locales->server)
				->fetchAll();
	}
	
	
	public function getAllGameroomsVersions()
	{
		return $this->connection->getSelectionFactory()
				->table('gameroom_version')
				->fetchAll();
	}
	
	
	/**
	 * @param string $gameroomId
	 * @param string $server
	 */
	public function deleteGameroom($gameroomId, $server)
	{
		$this->connection->query(		
				'DELETE FROM gameroom_version
				WHERE gameroom_id = ? AND server = ?',
				$gameroomId, $server
		);
	}
	
	
	public function saveGameroom($gameroom)
	{
		//get new version number
		$version = (int)$this->connection->query(
				'SELECT MAX(version) AS version FROM gameroom WHERE gameroom_id = ?',
				$gameroom->gameroom_id)
				->fetch()
				->version + 1;

		//insert gameroom
		$this->connection->getSelectionFactory()
				->table('gameroom')
				->insert(array(
					'gameroom_id' => $gameroom->gameroom_id,
					'filter_version_id' => NULL, //@todo
					'observer_version_id' => NULL, //@todo
					'version' => (int)$version,
					'created' => $this->locales->timestamp,
					'server' => $this->locales->server
				));

		//insert/update version record
		$this->readyGameroom($gameroom->gameroom_id, (int)$version, $this->locales->server);

		//texts
		$texts = array();
		foreach ($gameroom as $key => $value) {
			if (preg_match('/^(gameroom_name)_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
				$texts[] = array(
					'key' => str_replace('_', '-', $matches[1]) . '.' . $gameroom->gameroom_id . '.' . (int)$version,
					'lang' => $matches[2],
					'value' => $value
				);
			}
		}
		$this->connection->getSelectionFactory()
				->table('translation')
				->insert($texts);
	}
	
	
	public function readyGameroom($gameroomId, $version, $server)
	{
		$this->connection->query(
				'UPDATE gameroom_version SET version = ? WHERE gameroom_id = ? AND server = ?',
				(int)$version, $gameroomId, $server
		);
		$this->connection->query(
				'INSERT INTO gameroom_version (gameroom_id, version, server) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM gameroom_version WHERE gameroom_id = ? AND server = ?)',
				$gameroomId, (int)$version, $server, $gameroomId, $server
		);
	}
	
	
	public function getGameroomByVersionId($versionId)
	{
		return $this->connection->query(
				'SELECT gameroom.* FROM gameroom LEFT JOIN gameroom_version USING (gameroom_id, version) WHERE gameroom_version.gameroom_version_id = ?',
				(int)$versionId
				)->fetch();
	}
	
	
	public function getGameroomVersionId($gameroomId, $server)
	{
		return $this->connection->getSelectionFactory()
				->table('gameroom_version')
				->where('gameroom_id = ?', $gameroomId)
				->where('server = ?', $server)
				->fetch();
	}
	
	
	/**
	 * @return array
	 */
	public function getBots()
	{
		return $this->connection->getSelectionFactory()
				->table('bot')
				->fetchAll();
	}
	
	
	public function saveLocalVariable($variableId, $default, $description)
	{
		$this->connection->query(
				'UPDATE variable SET variable_id = ?, default_val = ?, description = ? WHERE variable_id = ?',
				$variableId, (string)$default, (string)$description, $variableId
		);
		$this->connection->query(
				'INSERT INTO variable (variable_id, default_val, description) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM variable WHERE variable_id = ?)',
				$variableId, (string)$default, (string)$description, $variableId
		);
	}
	
	
	public function saveGlobalVariable($name, $value, $description)
	{
		$this->connection->query(
				'UPDATE global_var SET name = ?, value = ?, description = ? WHERE name = ?',
				$name, (string)$value, (string)$description, $name
		);
		$this->connection->query(
				'INSERT INTO global_var (name, value, description) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM global_var WHERE name = ?)',
				$name, (string)$value, (string)$description, $name
		);
	}
	
	
	public function getNewFilterId()
	{
		return (int)$this->connection->query('SELECT MAX(filter_id) AS filter_id FROM filter')
				->fetch()->filter_id + 1;
	}
	
	
	public function getNewObserverId()
	{
		return (int)$this->connection->query('SELECT MAX(observer_id) AS observer_id FROM observer')
				->fetch()->observer_id + 1;
	}
	
	
	public function saveFilter($filter)
	{
		//get new version number
		$version = (int)$this->connection->query(
				'SELECT MAX(version) AS version FROM filter WHERE filter_id = ?',
				$filter->filter_id)
				->fetch()
				->version + 1;

		//insert filter
		$this->connection->getSelectionFactory()
				->table('filter')
				->insert(array(
					'filter_id' => $filter->filter_id,
					'variable_id' => $filter->variable_id,
					'global_var' => empty($filter->global_var) ? NULL : $filter->global_var,
					'time_start' => $filter->time_start,
					'time_end' => empty($filter->time_end) ? NULL : $filter->time_end,
					'level_min' => (int)$filter->level_min,
					'level_max' => empty($filter->level_max) ? NULL : (int)$filter->level_max,
					'influence_min' => (int)$filter->influence_min,
					'influence_max' => empty($filter->influence_max) ? NULL : (int)$filter->influence_max,
					'filter_script' => $filter->filter_script,
					'created' => $this->locales->timestamp,
					'server' => $this->locales->server,
					'version' => $version
				));

		//insert/update version record
		$this->readyFilter($filter->filter_id, (int)$version, $this->locales->server);

		//texts
		$texts = array();
		foreach ($filter as $key => $value) {
			if (preg_match('/^(filter_condition)_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
				$texts[] = array(
					'key' => str_replace('_', '-', $matches[1]) . '.' . $filter->filter_id . '.' . (int)$version,
					'lang' => $matches[2],
					'value' => $value
				);
			}
		}
		$this->connection->getSelectionFactory()
				->table('translation')
				->insert($texts);
	}
	
	
	protected function readyFilter($filterId, $version, $server)
	{
		$this->connection->query(
				'UPDATE filter_version SET version = ? WHERE filter_id = ? AND server = ?',
				(int)$version, (int)$filterId, $server
		);
		$this->connection->query(
				'INSERT INTO filter_version (filter_id, version, server) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM filter_version WHERE filter_id = ? AND server = ?)',
				(int)$filterId, (int)$version, $server, (int)$filterId, $server
		);
	}
	
	
	public function getFilterByVersionId($versionId)
	{
		return $this->connection->query(
				'SELECT filter.* FROM filter LEFT JOIN filter_version USING (filter_id, version) WHERE filter_version.filter_version_id = ?',
				(int)$versionId
				)->fetch();
	}
	
	
	public function getFilterVersionId($filterId, $server)
	{
		return $this->connection->getSelectionFactory()
				->table('filter_version')
				->where('filter_id = ?', (int)$filterId)
				->where('server = ?', $server)
				->fetch();
	}
	
	
	public function saveObserver($observer)
	{
		//get new version number
		$version = (int)$this->connection->query(
				'SELECT MAX(version) AS version FROM observer WHERE observer_id = ?',
				$observer->observer_id)
				->fetch()
				->version + 1;

		//insert observer
		$this->connection->getSelectionFactory()
				->table('observer')
				->insert(array(
					'observer_id' => $observer->observer_id,
					'description' => '', //@todo
					'effect_data' => $observer->effect_data,
					'created' => $this->locales->timestamp,
					'server' => $this->locales->server,
					'version' => $version
				));

		//insert/update version record
		$this->readyObserver($observer->observer_id, (int)$version, $this->locales->server);
	}
	
	
	protected function readyObserver($observerId, $version, $server)
	{
		$this->connection->query(
				'UPDATE observer_version SET version = ? WHERE observer_id = ? AND server = ?',
				(int)$version, (int)$observerId, $server
		);
		$this->connection->query(
				'INSERT INTO observer_version (observer_id, version, server) SELECT ?, ?, ? WHERE NOT EXISTS (SELECT 1 FROM observer_version WHERE observer_id = ? AND server = ?)',
				(int)$observerId, (int)$version, $server, (int)$observerId, $server
		);
	}
	
	
	public function getObserverByVersionId($versionId)
	{
		return $this->connection->query(
				'SELECT observer.* FROM observer LEFT JOIN observer_version USING (observer_id, version) WHERE observer_version.observer_version_id = ?',
				(int)$versionId
				)->fetch();
	}
	
	
	public function getObserverVersionId($observerId, $server)
	{
		return $this->connection->getSelectionFactory()
				->table('observer_version')
				->where('observer_id = ?', (int)$observerId)
				->where('server = ?', $server)
				->fetch();
	}
	
	
	/**
	 * @return array
	 */
	public function getCountries()
	{
		return $this->connection->getSelectionFactory()
				->table('translation')
				->where('key LIKE ?', 'country.%')
				->where('lang = ?', $this->locales->lang)
				->fetchAll();
	}
}
