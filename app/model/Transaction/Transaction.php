<?php

namespace Model\Transaction;

use Nette;



/**
 * @method \stdClass getData()
 * @method setData(\stdClass)
 */
class Transaction extends Nette\Object
{
	/** @var \stdClass */
	private $data;
	
	
	
	public function toArray()
	{
		return (array)$this->data;
	}
}
