<?php

namespace Framework\Kapafaa\Effects;


/**
 * @method int getAvatarId()
 */
class AddAvatar extends WorldEffect
{
	const ADD_AVATAR = 'addAvatar';
	
	/** @var int */
	private $avatarId;
	
	
	/**
	 * @param int $avatarId
	 */
	public function __construct($avatarId)
	{
		parent::__construct(self::ADD_AVATAR);
		$this->avatarId = $avatarId;
	}
	
	
	public function __toString() {
		return parent::__toString() . ' ' . $this->avatarId;
	}
}
