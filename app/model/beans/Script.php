<?php

namespace Model\Beans;

use Nette;



class Script extends Nette\Object
{
	/** @var array(\Model\Beans\ScriptLine) */
	private $lines;
}