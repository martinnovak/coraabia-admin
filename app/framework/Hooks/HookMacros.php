<?php

namespace Framework\Hooks;

use Nette\Latte;



class HookMacros extends Latte\Macros\MacroSet
{
	/**
	 * @param \Nette\Latte\Engine $engine 
	 */
	public static function setup(Latte\Engine $engine)
	{
		self::install($engine->getCompiler());
	}

	
	
	/**
	 * @param \Nette\Latte\Compiler $compiler 
	 */
	public static function install(Latte\Compiler $compiler)
	{
		$me = parent::install($compiler);
		
		$me->addMacro('hook', array($me, 'macroHook'));
	}
	
	
	
	public function macroHook(Latte\MacroNode $node, Latte\PhpWriter $writer)
	{
		return $writer->write('$_hookTmp = new \Framework\Hooks\TemplateHook(%node.args); $template->hookManager->fire(%node.word, $_hookTmp); $_hookTmp->render();');
	}
}