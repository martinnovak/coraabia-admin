<?php

namespace Framework\Kapafaa\Operators;

use Framework\Kapafaa\Object;


abstract class Operator extends Object
{
	public static $regular = '(add|remove|\=\<|\=\>|\<|\>|\=|\*\=|\+\=|\-\=|\/\=)';
}
