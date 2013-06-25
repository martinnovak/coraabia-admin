<?php

namespace App;

use Nette,
	Framework;



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
			$start[$card->valid_from ? strtotime((string)$card->valid_from, $this->locales->timestamp) : 0][] = $card;
			$finish[$card->valid_to ? strtotime((string)$card->valid_to, $this->locales->timestamp) : 0][] = $card;
		}
		$keys = array_unique(array_keys($start) + array_keys($finish));
		rsort($keys);
		$template->start = $start;
		$template->finish = $finish;
		$template->keys = $keys;
		
		$template->render();
	}
}
