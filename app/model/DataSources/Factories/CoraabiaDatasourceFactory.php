<?php

namespace Model\DataSources\Factories;

use Nette,
	Model;


class CoraabiaDatasourceFactory extends Nette\Object implements IDatasourceFactory
{
	/** @var \Nette\DI\Container */
	private $context;
	
	/** @var \Model\Locales */
	private $locales;
		
	
	/**
	 * @param \Nette\DI\Container $context
	 * @param \Model\Locales $locales 
	 */
	public function __construct(Nette\DI\Container $context, Model\Locales $locales)
	{
		$this->context = $context;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Model\DataSources\ISource
	 */
	public function access()
	{
		return $this->context->getService('source.coraabia.' . $this->locales->server);
	}
}
