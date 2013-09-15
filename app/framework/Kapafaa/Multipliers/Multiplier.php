<?php

namespace Framework\Kapafaa\Multipliers;

use Framework\Kapafaa\Object;


abstract class Multiplier extends Object
{
	public static $regular = '(, multiply\.\w+)?';
}
