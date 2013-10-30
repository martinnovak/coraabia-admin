<?php

namespace Model\Datasource;

use Nette,
	Framework;


class MapiSource extends Nette\Object implements ISource
{
	/** @var \Framework\Mapi\MapiRequestFactory */
	private $mapiRequestFactory;
	
	
	/**
	 * @param \Framework\Mapi\MapiRequestFactory $mapiRequestFactory
	 */
	public function __construct(Framework\Mapi\MapiRequestFactory $mapiRequestFactory)
	{
		$this->mapiRequestFactory = $mapiRequestFactory;
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequestFactory
	 */
	public function getSource()
	{
		return $this->mapiRequestFactory;
	}
}
