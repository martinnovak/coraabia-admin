<?php

namespace Model\Datasource;

use Nette,
	Framework;


class MapiSource extends Nette\Object implements ISource
{
	/** @var \Framework\Mapi\MapiRequestFactory */
	private $factory;
	
	
	/**
	 * @param \Framework\Mapi\MapiRequestFactory $factory
	 */
	public function __construct(Framework\Mapi\MapiRequestFactory $factory)
	{
		$this->factory = $factory;
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequestFactory
	 */
	public function getSource()
	{
		return $this->factory;
	}
}
