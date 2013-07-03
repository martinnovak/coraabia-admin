<?php

namespace Coraabia\Mapi;

use Nette,
	Model;



class MapiRequestFactory extends Nette\Object
{
	/** @var \Model\Locales */
	private $locales;
	
	/** @var array */
	private $urls;
	
	
	
	/**
	 * @param \Model\Locales $locales
	 * @param string $urls 
	 */
	public function __construct(Model\Locales $locales, $urls)
	{
		$this->locales = $locales;
		$this->urls = $urls;
	}
	
	
	
	/**
	 * @param array $args
	 * @param string $retColumn
	 * @return \Coraabia\Mapi\MapiRequest 
	 */
	public function create(array $args, $retColumn)
	{
		return new MapiRequest($this->urls[$this->locales->server], $args, $retColumn);
	}
}