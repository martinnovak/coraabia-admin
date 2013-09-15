<?php

namespace Framework\Kapafaa\Modifications;

use Framework\Kapafaa\Object;


abstract class Modification extends Object
{
	public static $regular = '((?:add|remove|\=\<|\=\>|\<|\>|\=|\*\=|\+\=|\-\=|\/\=) (?:[^\(\,]+))';
}
