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
	
	
	public function createConnection($connection)
	{
		try {
			$this->connection->beginTransaction();
			
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
			$this->readyConnectionInternal($connection->connection_id, (int)$version, $this->locales->server);
			
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
			
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}
	
	
	public function readyConnection($connectionId, $version, $server)
	{
		try {
			$this->connection->beginTransaction();
			$this->readyConnectionInternal($connectionId, $version, $server);
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}
	
	
	protected function readyConnectionInternal($connectionId, $version, $server)
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
	
	
	public function createGameroom($gameroom)
	{
		try {
			$this->connection->beginTransaction();
			
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
			$this->readyGameroomInternal($gameroom->gameroom_id, (int)$version, $this->locales->server);
			
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
			
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}
	
	
	public function readyGameroom($gameroomId, $version, $server)
	{
		try {
			$this->connection->beginTransaction();
			$this->readyGameroomInternal($gameroomId, $version, $server);
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}
	
	
	protected function readyGameroomInternal($gameroomId, $version, $server)
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
	
	
	/**
	 * @return array
	 */
	public function getBots()
	{
		return $this->connection->getSelectionFactory()
				->table('bot')
				->fetchAll();
	}
	
	
	public function getFilterByVersionId($versionId)
	{
		return $this->connection->getSelectionFactory()
				->table('filter')
				->where(':filter_version.filter_version_id = ?', (int)$versionId)
				->fetch();
	}
}
