<?php

namespace Model;

use Nette,
	Model\DataSources;


/**
 * @method \Model\Locales getLocales()
 * @method \Model\DataSources\ISource getDatasource()
 */
abstract class Model extends Nette\Object
{
	/** @var \Model\DataSources\ISource */
	private $datasource;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Model\DataSources\ISource $source
	 * @param \Model\Locales $locales
	 */
	public function __construct(DataSources\ISource $source, Locales $locales) {
		$this->datasource = $source;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->datasource->getSource();
	}
}
