<?php

namespace Model\DataSources\Factories;

use Nette,
	Model,
	Coraabia;


class GameDatasourceFactory extends Nette\Object implements IDatasourceFactory
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
	 * Returns database game when server is empty for translator.
	 * @todo ^^ Maybe better?
	 * @return \Model\DataSources\ISource
	 */
	public function access()
	{
		switch ($this->locales->module) {
			case Coraabia\ModuleEnum::CORAABIA:
				return $this->context->getService('source.game.' . $this->locales->server);
				break;
			default:
				return $this->context->getService('source.game');
		}
	}
}
