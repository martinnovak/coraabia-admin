<?php

namespace Model\DataSources;

use Nette,
	Framework,
	Model;


class MapiSource extends Nette\Object implements ISource
{
	/** @var \Framework\Mapi\MapiRequestFactory */
	private $mapiRequestFactory;
	
	/** @var \Model\Locales */
	private $locales;
	
	
	/**
	 * @param \Framework\Mapi\MapiRequestFactory $mapiRequestFactory
	 * @param \Model\Locales
	 */
	public function __construct(Framework\Mapi\MapiRequestFactory $mapiRequestFactory, Model\Locales $locales)
	{
		$this->mapiRequestFactory = $mapiRequestFactory;
		$this->locales = $locales;
	}
	
	
	/**
	 * @return \Framework\Mapi\MapiRequestFactory
	 */
	public function getSource()
	{
		return $this->mapiRequestFactory;
	}
	
	
	/**
	 * @return array
	 */
	public function getTransactions()
	{
		return $this->getSource()->create('FIND_TRANSACTION', 'findTransactionResponse.transactions')
				->setParam('timestamp', 0)
				->setParam('counter', 0)
				->setParam('findTransactionFilter', array('types' => array()))
				->load();
	}
}
