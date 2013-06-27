<?php

namespace App;

use Nette,
	Framework,
	Grido;



class CardControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Model @inject */
	public $game;
	
	
	
	public function renderTimeLine()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/timeLine.latte');
		
		$start = $finish = $dates = array();
		foreach ($this->game->cards->orderBy('translated_name')->asc()->fetchAll() as $card) {
			$from = $card->valid_from ? $card->valid_from->setTime(0, 0, 0) : 0;
			$to = $card->valid_to ? $card->valid_to->setTime(0, 0, 0) : 0;
			$start[strtotime((string)$from, $this->locales->timestamp)][] = $card;
			$finish[strtotime((string)$to, $this->locales->timestamp)][] = $card;
		}
		$keys = array_unique(array_keys($start) + array_keys($finish));
		rsort($keys);
		$template->start = $start;
		$template->finish = $finish;
		$template->keys = $keys;
		
		$template->render();
	}
	
	
	
	public function renderSpoiler()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/spoiler.latte');
		$template->render();
	}
	
	
	
	public function createComponentSpoiler($name)
	{
		$grido = new Grido\Grid($this, $name);
		$grido->setModel($this->game->cards)
				->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setPrimaryKey('card_id')
				->setFilterRenderType(Grido\Components\Filters\Filter::RENDER_INNER)
				->setDefaultSort(array('translated_name' => 'asc'));
		
		$grido->addColumn('card_id', 'ID');
		$grido->addColumn('translated_name', 'Jméno')
				->setSortable()
				->setFilter();
		
		return $grido;
	}
}
