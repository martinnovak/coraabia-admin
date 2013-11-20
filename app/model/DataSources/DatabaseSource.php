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
}
