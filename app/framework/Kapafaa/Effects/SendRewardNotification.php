<?php

namespace Framework\Kapafaa\Effects;


class SendRewardNotification extends WorldEffect
{
	const SEND_REWARD_NOTIFICATION = 'sendRewardNotification';
	
	
	public function __construct()
	{
		parent::__construct(self::SEND_REWARD_NOTIFICATION);
	}
	
	
	public function __toString() {
		return parent::__toString() . ' @completed@';
	}
}
