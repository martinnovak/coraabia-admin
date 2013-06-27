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
		$self = $this;
		$baseUri = $this->template->baseUri;
		
		$grido = new Grido\Grid($this, $name);
		$grido->setModel($this->game->cards)
				->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setPrimaryKey('card_id')
				->setDefaultSort(array('type' => 'asc', 'fraction' => 'asc', 'rarity' => 'asc', 'translated_name' => 'asc'));
		
		$grido->addColumn('card_id', 'ID')
				->setSortable();
		
		$grido->addColumn('translated_name', 'Jméno')
				->setSortable()
				->setCustomRender(function ($item) {
					return '<span class="' . strtolower($item->fraction) . '">' . trim($item->translated_name) . '</span>';
				});
				
		$grido->addColumn('points', 'B')
				->setSortable();

		$grido->addColumn('danger', 'N')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\Card::CHARACTER ? $item->danger : '';
				});
		
		$grido->addColumn('intellect', 'I')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\Card::CHARACTER ? $item->intellect : '';
				});
		
		$grido->addColumn('vitality', 'V')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\Card::CHARACTER ? $item->vitality : '';
				});
		
		$grido->addColumn('karma', 'K')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\Card::CHARACTER ? $item->karma : '';
				});
		
		$grido->addColumn('rarity', 'R')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->rarity[0];
				});
		
		$grido->addColumn('type', 'T')
				->setSortable()
				->setCustomRender(function ($item) use ($baseUri) {
					return \Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/" .
							($item->type == \Coraabia\Card::CHARACTER ? 'card' : 'trick') .
							".png");
				});
		
		$grido->addColumn('fraction', 'F')
				->setSortable()
				->setCustomRender(function ($item) use ($baseUri) {
					return \Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/" .
							($item->fraction ? strtolower($item->fraction) : 'card') .
							".png");
				});
		
		$grido->addColumn('subtype', 'S')
				->setSortable()
				->setCustomRender(function ($item) {
					return ucfirst(strtolower($item->subtype));
				});
				
		$grido->addColumn('ready', '')
				->setCustomRender(function ($item) {
					return $item->ready ? '<i class="icon-ok"></i>' : '';
				});
		
		if ($this->locales->server == \Coraabia\Server::DEV) {
			$grido->addAction('edit', '')
					->setIcon('edit')
					->setCustomHref(function ($item) use ($self) {
						return $self->getPresenter()->lazyLink('showUpdateCard', array('id' => $item->card_id));
					});
			$grido->addAction('remove', '')
					->setIcon('remove')
					->setCustomHref(function ($item) use ($self) {
						return $self->getPresenter()->lazyLink('deleteCard', array('id' => $item->card_id));
					})
					->setConfirm(function ($item) {
						return "Opravdu chcete smazat kartu '$item->translated_name'?";
					});
		}
		
		return $grido;
	}
}
