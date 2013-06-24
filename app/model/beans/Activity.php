<?php

namespace Model\Beans;

use Nette;



class Activity extends Nette\Object
{
	/** @var int */
	private $id;
	
	/** @var array(\Model\Beans\Filter) */
	private $filters;
	
	/** @var array(\Model\Beans\Filter) */
	private $filtersDisabled;
	
	/** @var array(\Model\Beans\Observer) */
	private $observers;
	
	/** @var array(\Model\Beans\Gameroom) */
	private $gamerooms;
}