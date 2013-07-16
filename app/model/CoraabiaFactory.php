<?php

namespace Model;

use Nette;



class CoraabiaFactory extends Nette\Object
{
	/** @var array */
	private $dbs;
	
	/** @var \Model\Locales */
	private $locales;
		
	
	
	/**
	 * @param array $dbs
	 * @param \Model\Locales $locales 
	 */
	public function __construct(array $dbs, Locales $locales)
	{
		$this->dbs = $dbs;
		$this->locales = $locales;
	}
	
	
	
	/**
	 * @return \Model\Coraabia 
	 */
	public function access()
	{
		return $this->dbs[$this->locales->server];
	}
}
