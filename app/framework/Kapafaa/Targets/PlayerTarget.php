<?php

namespace Framework\Kapafaa\Targets;

use Framework\Kapafaa\Object;


abstract class PlayerTarget extends Object
{
	public static $regular = '(me|opp|both)';
	/**
	 * @param array $classes
	 * @param array $remaining
	 * @return string
	 */
	public static function findImplementation(array $classes, array $remaining) {
		switch ($remaining[0]) {
			case 'me': return '\Framework\Kapafaa\Targets\Me'; break;
			case 'opp': return '\Framework\Kapafaa\Targets\Opp'; break;
			case 'both': return '\Framework\Kapafaa\Targets\Both'; break;
		}
	}
}
