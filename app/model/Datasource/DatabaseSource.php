<?php

namespace Model\Datasource;

use Nette;


class DatabaseSource extends Nette\Object implements ISource
{
	/** @var \Nette\Database\Connection */
	private $connection;
		
	
	/**
	 * @param \Nette\Database\Connection $connection
	 */
	public function __construct(Nette\Database\Connection $connection)
	{
		$this->connection = $connection;
	}
	
	
	/**
	 * @return \Nette\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}
	
	
	/**
	 * @throws Nette\NotSupportedException
	 */
	public function getElement()
	{
		throw new Nette\NotSupportedException("DatabaseSource does not support getElement method.");
	}
	
	
	/**
	 * @return \Nette\Database\SelectionFactory 
	 */
	public function getSelectionFactory()
	{
		return $this->connection->selectionFactory;
	}
}
