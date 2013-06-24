<?php

namespace Model\Beans;

use Nette;



class ScriptLine extends Nette\Object
{
	/** @var array(\Model\Beans\Variable) */
	private $variables;
	
	/** @var string */
	private $text;
}