<?php

namespace Model;

use Nette,
	Framework;


/**
 * @method \Framework\Xml\XmlElement getElement()
 * @method \Model\Locales getLocales()
 */
abstract class XmlModel extends Nette\Object
{
	/** @var \Framework\Xml\XmlElement */
	private $element;
	
	/** @var \Model\Locales */
	private $locales;
		
	/** @var string */
	protected $filename;
	
	
	/**
	 * @param \Framework\Xml\XmlElement $element
	 * @param string $filename
	 * @param \Model\Locales $locales
	 */
	public function __construct(Framework\Xml\XmlElement $element, Locales $locales, $filename = '')
	{
		$this->element = $element;
		$this->locales = $locales;
		$this->filename = $filename;
	}
}
