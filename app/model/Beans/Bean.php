<?php

namespace Model\Beans;

use Nette;



/**
 * @method setData(array) 
 * @method array getData()
 */
abstract class Bean extends Nette\Object
{
	private $data;
	
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
}
