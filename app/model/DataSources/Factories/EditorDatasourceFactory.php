<?php

namespace Model\DataSources\Factories;

use Nette;


class EditorDatasourceFactory extends Nette\Object implements IDatasourceFactory
{
	/** @var \Nette\DI\Container */
	private $context;
	
	
	/**
	 * @param \Nette\DI\Container $context
	 */
	public function __construct(Nette\DI\Container $context)
	{
		$this->context = $context;
	}
	
	
	/**
	 * @return \Model\DataSources\ISource
	 */
	public function access()
	{
		return $this->context->getService('source.editor');
	}
}
