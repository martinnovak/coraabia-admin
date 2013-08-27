<?php

namespace Model\Datasource;

use Nette,
	Framework;


class XmlSource extends Nette\Object implements ISource
{
	/** @var \Framework\Xml\XmlElement */
	private $element;
	
	/** @var string */
	protected $filename;
	
	
	/**
	 * @param \Framework\Xml\XmlElement $element
	 * @param string $filename
	 */
	public function __construct(Framework\Xml\XmlElement $element, $filename = '')
	{
		$this->element = $element;
		$this->filename = $filename;
	}
	
	
	/**
	 * @return \Framework\Xml\XmlElement
	 */
	public function getSource()
	{
		return $this->element;
	}
}
