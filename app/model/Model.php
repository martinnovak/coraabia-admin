<?php

namespace Model;

use Nette;


/**
 * @method \Nette\Database\Connection getConnection()
 * @method \Model\Locales getLocales()
 */
abstract class Model extends Nette\Object
{
	/** @var \Nette\Database\Connection */
	private $connection;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Nette\Database\Connection $connection
	 * @param \Model\Locales $locales 
	 */
	public function __construct(Nette\Database\Connection $connection, Locales $locales)
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
