<?php

namespace Model;

use Nette,
	Model\Datasource;


/**
 * @method \Model\Locales getLocales()
 */
abstract class Model extends Nette\Object implements Datasource\ISource
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
	

	/**
	 * @return \Nette\Database\Connection
	 * @throws \Nette\NotSupportedException
	 */
	public function getConnection()
	{
		return $this->datasource->getConnection();
	}
	
	
	/**
	 * @return \Framework\Xml\XmlElement
	 * @throws \Nette\NotSupportedException
	 */
	public function getElement()
	{
		return $this->datasource->getElement();
	}
	
	
	/**
	 * @return \Nette\Database\SelectionFactory
	 * @throws \Nette\NotSupportedException
	 */
	public function getSelectionFactory()
	{
		return $this->datasource->getSelectionFactory();
	}
}
