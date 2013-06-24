<?php

namespace Model;

use Nette;



/**
 * @method \DibiConnection getConnection()
 * @method \Model\Locales getLocales()
 */
abstract class Model extends Nette\Object
{
	/** @var \DibiConnection */
	private $connection;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	
	/**
	 * @param \DibiConnection $connection
	 * @param \Model\Locales $locales 
	 */
	public function __construct(\DibiConnection $connection, Locales $locales)
	{
		$this->connection = $connection;
		$this->locales = $locales;
	}
}