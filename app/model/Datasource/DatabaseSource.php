<?php

namespace Model\Datasource;

use Nette;


/**
 * @method \Nette\Database\Connection getConnection()
 * @method \Model\Locales getLocales()
 */
abstract class DatabaseSource extends Nette\Object implements ISource
{
	/** @var \Nette\Database\Connection */
	private $connection;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Nette\Database\Connection $connection
	 * @param \Model\Locales $locales 
	 */
	public function __construct(Nette\Database\Connection $connection, \Model\Locales $locales)
	{
		$this->connection = $connection;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Nette\Database\SelectionFactory 
	 */
	public function getSelectionFactory()
	{
		return $this->connection->selectionFactory;
	}
}
