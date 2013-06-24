<?php

namespace Model\Beans;

use Nette;



class Filter extends Nette\Object
{
	/** @var int */
	private $id;
	
	/** @var \Model\Beans\Variable */
	private $variable;
}