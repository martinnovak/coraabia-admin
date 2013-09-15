<?php

namespace Framework\Kapafaa\Effects;


/**
 * @kapafaa eff.world.addAvatar %avatar%
 */
class AddAvatar extends Effect
{
	/** @var int */
	private $avatar;
	
	
	/**
	 * @param int $avatar
	 */
	public function __construct($avatar)
	{
		$this->avatar = $avatar;
	}
}
