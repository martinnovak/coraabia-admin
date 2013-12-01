<?php

namespace Framework\Latte\Macros;

use Nette\Latte,
	Nette\Latte\Macros\MacroSet,
	Nette\Latte\MacroNode,
	Nette\Latte\PhpWriter;


class UIMacros extends MacroSet
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
		
		$me->addMacro('ifAuthorized', array($me, 'macroIfAuthorized'), array($me, 'macroIfAuthorizedEnd'));
		
		$me->addMacro('href', NULL, NULL, function(MacroNode $node, PhpWriter $writer) use ($me) {
			return ' ?> href="<?php ' . $me->macroLink($node, $writer) . ' ?>"<?php ';
		});
		$me->addMacro('plink', array($me, 'macroLink'));
		$me->addMacro('link', array($me, 'macroLink'));
	}
	

	/**
	 * {ifAuthorized destination [,] [params]}
	 * {ifAuthorized}
	 */
	public function macroIfAuthorized(MacroNode $node, PhpWriter $writer)
	{
		$parent = $node->parentNode;
		while ($parent) {
			if ($parent->name === 'ifAuthorized') {
				throw new Latte\CompileException("{ifAuthorized} already opened and cannot be nested");
			}
			$parent = $parent->parentNode;
		}

		if ($node->data->capture = ($node->args === '')) {
			return '$_l->notAuthorizedLinks = 0; ob_start();';
		} else {
			return $writer->write('try { $_presenter->link(%node.word, %node.array?); } catch (Nette\Application\UI\InvalidLinkException $e) {}')
					. 'if ($_presenter->checkRequestRequirements($_presenter->lastCreatedRequest)):';
		}
	}


	/**
	 * {/ifAuthorized}
	 */
	public function macroIfAuthorizedEnd(MacroNode $node, PhpWriter $writer)
	{
		if ($node->data->capture) {
			return 'if ($_l->notAuthorizedLinks) ob_end_clean(); else ob_end_flush(); $_l->notAuthorizedLinks = NULL;';
		} else {
			return 'endif';
		}
	}
	
	
	/**
	 * {link destination [,] [params]}
	 * {plink destination [,] [params]}
	 * n:href="destination [,] [params]"
	 */
	public function macroLink(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('echo %escape(%modify(' . ($node->name === 'plink' ? '$_presenter' : '$_control') . '->link(%node.word, %node.array?)));')
				. 'if (isset($_l->notAuthorizedLinks)) \Framework\Latte\Macros\UIMacros::checkLinkPermissions($_presenter, $_l);';
	}
	
	
	/**
	 * @param \Nette\Application\UI\Presenter $presenter
	 * @param \stdClass $local
	 */
	public static function checkLinkPermissions($presenter, $local) {
		if ($presenter instanceof \Framework\Application\UI\BasePresenter) {
			if ($presenter->checkRequestRequirements($presenter->lastCreatedRequest) !== TRUE) {
				$local->notAuthorizedLinks++;
			}
		}
	}
}
