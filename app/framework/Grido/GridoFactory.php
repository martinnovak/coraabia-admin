<?php

namespace Framework\Grido;

use Nette,
	Grido;


class GridoFactory extends Nette\Object
{
	/** @var \Nette\Localization\ITranslator */
	private $translator;
	
	
	/**
	 * @param \Nette\Localization\ITranslator $translator 
	 */
	public function __construct(Nette\Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}
	
	
	/**
	 * @param \Nette\ComponentModel\IContainer $parent
	 * @param string|NULL $name
	 * @return \Grido\Grid 
	 */
	public function create(Nette\ComponentModel\IContainer $parent = NULL, $name = NULL)
	{
		$grido = new \Grido\Grid($parent, $name);
		$grido->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setFilterRenderType(Grido\Components\Filters\Filter::RENDER_OUTER);
		return $grido;
	}
}
