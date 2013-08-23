<?php

namespace Model\Datasource;

use Nette,
	Framework;


/**
 * @method \Framework\Xml\XmlElement getElement()
 * @method \Model\Locales getLocales()
 */
abstract class XmlSource extends Nette\Object implements ISource
{
	/** @var \Framework\Xml\XmlElement */
	private $element;
	
	/** @var \Model\Locales */
	private $locales;
		
	/** @var string */
	protected $filename;
	
	
	/**
	 * @param \Framework\Xml\XmlElement $element
	 * @param \Model\Locales $locales
	 * @param string $filename
	 */
	public function __construct(Framework\Xml\XmlElement $element, \Model\Locales $locales, $filename = '')
	{
		$this->element = $element;
		$this->locales = $locales;
		$this->filename = $filename;
	}
}
