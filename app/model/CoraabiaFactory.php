<?php

namespace Model;

use Nette;


class CoraabiaFactory extends Nette\Object
{
	/** @var \Nette\DI\Container */
	private $context;
	
	/** @var \Model\Locales */
	private $locales;
		
	
	/**
	 * @param \Nette\DI\Container $context
	 * @param \Model\Locales $locales 
	 */
	public function __construct(Nette\DI\Container $context, Locales $locales)
	{
		$this->context = $context;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Model\Coraabia 
	 */
	public function access()
	{
		return $this->context->getService($this->locales->server);
	}
}
