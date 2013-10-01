<?php

namespace Framework\Localization;

use Nette;


interface ICachingTranslator extends Nette\Localization\ITranslator
{
	
	public function clean();
}