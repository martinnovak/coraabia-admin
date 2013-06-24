<?php

namespace Model\Beans;

use Nette;



class Observer extends Nette\Object
{
	/** @var int */
	private $id;
	
	/** @var array(\Model\Beans\Script) */
	private $scripts;
}