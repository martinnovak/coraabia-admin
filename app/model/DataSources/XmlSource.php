<?php

namespace Model\DataSources;

use Nette,
	Framework,
	Model;


class XmlSource extends Nette\Object implements ISource
{
	/** @var \Framework\Xml\XmlElement */
	private $element;
	
	/** @var string */
	protected $filename;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Framework\Xml\XmlElement $element
	 * @param string $filename
	 * @param \Model\Locales
	 */
	public function __construct(Framework\Xml\XmlElement $element, $filename = '', Model\Locales $locales)
	{
		$this->element = $element;
		$this->filename = $filename;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Framework\Xml\XmlElement
	 */
	public function getSource()
	{
		return $this->element;
	}
	
	
	public function beginTransaction()
	{
		//not supported
	}
	
	
	public function commit()
	{
		//not supported
	}
	
	
	public function rollBack()
	{
		//not supported
	}
}
