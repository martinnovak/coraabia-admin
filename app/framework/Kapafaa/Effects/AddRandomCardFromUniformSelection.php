<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addRandomCardFromUniformSelection #%edition%#
 */
class AddRandomCardFromUniformSelection extends Effect
{
	/** @var string */
	private $edition;
	
	
	/**
	 * @param string $edition
	 */
	public function __construct($edition)
	{
		$this->edition = $edition;
	}
}
