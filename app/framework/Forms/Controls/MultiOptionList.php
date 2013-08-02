<?php

namespace Framework\Forms\Controls;

use Nette,
	Nextras;



class MultiOptionList extends Nextras\Forms\Controls\MultiOptionList
{
	
	public function __construct($label = NULL, array $items = NULL)
	{
		parent::__construct($label, $items);
		$this->container = Nette\Utils\Html::el('div')->addClass('multioption');
		$this->itemContainer = Nette\Utils\Html::el('');
	}
	
	
	
	public function getLabelItem($key, $caption = NULL)
	{
		$label = parent::getLabelItem($key, $caption);
		$label->class = 'checkbox';
		return $label;
	}
}
