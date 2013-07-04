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
	 * @param array $urls 
	 */
	public function __construct(Model\Locales $locales, array $urls)
	{
		$this->locales = $locales;
		$this->urls = $urls;
	}
	
	
	
	/**
	 * @param string $id
	 * @param string $retColumn
	 * @return \Coraabia\Mapi\MapiRequest 
	 */
	public function create($id, $retColumn)
	{
		return new MapiRequest($this->urls[$this->locales->server], array('id' => $id), $retColumn);
	}
}
