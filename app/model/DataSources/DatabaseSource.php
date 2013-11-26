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
		return $this->connection->getSelectionFactory()
				->table('activity')
				->fetchAll();
	}
	
	
	/**
	 * @return array
	 */
	public function getConnections()
	{
		return $this->connection->query(
				'SELECT
					connection.*
				FROM connection
				LEFT JOIN connection_version USING (connection_id, version)
				WHERE
					connection_version.server = ?',
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
				WHERE
						connection_id = ? AND
						server = ?',
					$connectionId, $server);
	}
	
	
	public function createConnection(array $connection, $server)
	{
		try {
			$this->connection->beginTransaction();
			
			$version = (int)$this->connection->query(
					'SELECT MAX(version) AS version FROM connection WHERE connection_id = ?',
					$connection['connection_id'])
					->fetch()
					->version + 1;
			
			$this->connection->getSelectionFactory()
					->table('connection')
					->insert(array(
						'connection_id' => $connection['connection_id'],
						'influence_cost' => (int)$connection['influence_cost'],
						'art_id' => empty($connection['art_id']) ? NULL : (int)$connection['art_id'],
						'type' => $connection['type'],
						'effect_data' => $connection['effect_data'],
						'version' => $version,
						'created' => $this->locales->timestamp,
						'server' => $server
					));
			
			if ($this->getConnectionVersion($connection['connection_id'], $server)) {
				$this->connection->query(
						'UPDATE connection_version SET version = ? WHERE connection_id = ? AND server = ?',
						$version,
						$connection['connection_id'],
						$server
				);
			} else {
				$this->connection->getSelectionFactory()
						->table('connection_version')
						->insert(array(
							'connection_id' => $connection['connection_id'],
							'version' => $connection['version'],
							'server' => $server
						));
			}
			
			//texts
			$texts = array();
			foreach ($connection as $key => $value) {
				if (preg_match('/^(connection_name|connection_description|connection_tooltip)_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
					$texts[] = array(
						'key' => str_replace('_', '-', $matches[1]) . '.' . $connection['connection_id'] . '.' . $version,
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
		if ($this->getConnectionVersion($connectionId, $server)) {
			return $this->connection->query(
					'UPDATE connection_version SET version = ? WHERE connection_id = ? AND server = ?',
					(int)$version, $connectionId, $server
			);
		} else {
			return $this->connection->getSelectionFactory()
					->table('connection_version')
					->insert(array(
						'connection_id' => $connectionId,
						'version' => $version,
						'server' => $server
					));
		}
	}
	
	
	protected function getConnectionVersion($connectionId, $server)
	{
		return $this->connection->getSelectionFactory()
				->table('connection_version')
				->where('connection_id = ?', $connectionId)
				->where('server = ?', $server)
				->fetch();
	}
	
	
	public function getConnectionVersions()
	{
		return $this->connection->getSelectionFactory()
				->table('connection_version')
				->fetchAll();
	}
}
