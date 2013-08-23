<?php

namespace Model;

use Nette,
	Model\Datasource;


/**
 * @method \Model\Locales getLocales()
 */
abstract class Model extends Nette\Object
{
	/** @var \Model\Datasource\ISource */
	private $datasource;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Model\Datasource\ISource $source
	 * @param \Model\Locales $locales
	 */
	public function __construct(Datasource\ISource $source, Locales $locales) {
		$this->datasource = $source;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Model\Datasource\ISource
	 */
	public function getDataSource()
	{
		return $this->datasource;
	}
}
