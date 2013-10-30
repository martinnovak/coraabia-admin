<?php

namespace App\GameModule;

use Framework;


class CardControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	
	public function renderTimeLine()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/timeLine.latte');
		$this->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate('\Nette\Templating\FileTemplate')
					->setFile(__DIR__ . '/timeLineScripts.latte');
			$hook->addTemplate($tmpl);
		});
		
		$start = $finish = $dates = array();
		foreach ($this->game->getCards()->fetchAll() as $card) {
			$from = $card->valid_from ? $card->valid_from->setTime(0, 0, 0) : 0;
			$to = $card->valid_to ? $card->valid_to->setTime(0, 0, 0) : 0;
			$start[strtotime((string)$from)][] = $card;
			$finish[strtotime((string)$to)][] = $card;
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
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentSpoiler($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('editCard');
		$removeLink = $this->lazyLink('deleteCard');
		$artistLink = $this->getPresenter()->lazyLink('Image:editArtist');
		$baseUri = $this->template->baseUri;
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->game->getCards())
				->setPrimaryKey('card_id')
				->setDefaultSort(array('type' => 'ASC', 'fraction' => 'ASC', 'rarity' => 'ASC'));
		
		$grido->addColumnNumber('card_id', 'ID')
				->setSortable();
		
		$grido->addColumn('translated_name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->card_id) . '" class="' . strtolower($item->fraction) . '">' . trim($self->translator->translate('card.' . $item->card_id)) . '</a>';
				});
				
		$grido->addColumnNumber('points', 'B')
				->setSortable();

		$grido->addColumnNumber('danger', 'N')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\CardTypeEnum::CHARACTER ? $item->danger : '';
				});
		
		$grido->addColumnNumber('intellect', 'I')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\CardTypeEnum::CHARACTER ? $item->intellect : '';
				});
		
		$grido->addColumnNumber('vitality', 'V')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\CardTypeEnum::CHARACTER ? $item->vitality : '';
				});
		
		$grido->addColumnNumber('karma', 'K')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->type == \Coraabia\CardTypeEnum::CHARACTER ? $item->karma : '';
				});
		
		$grido->addColumn('rarity', 'R')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->rarity[0];
				});
		
		$grido->addColumn('type', 'T')
				->setSortable()
				->setCustomRender(function ($item) use ($self, $baseUri) {
					$result = \Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/"
							. ($item->type == \Coraabia\CardTypeEnum::CHARACTER ? 'card' : 'trick')
							. ".png");
					switch ($item->type) {
						case \Coraabia\CardTypeEnum::TRICK_WIN:
							$result .= '&nbsp;<span title="'
								. $self->translator->translate('Body')
								. '">&#9898;</span>';
							break;
						case \Coraabia\CardTypeEnum::TRICK_NOW:
							$result .= '&nbsp;<span title="'
								. $self->translator->translate('Skóre')
								. '">&#9723;</span>';
							break;
					}
					return $result;
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
		
		$grido->addColumn('edition_id', 'E')
				->setSortable()
				->setCustomRender(function ($item) use ($self) {
					return $self->translator->translate('edition.' . $item->edition_id);
				});

		//Cache artists, otherwise it's very slow.
		$artists = array();
		foreach ($this->game->getArts()
				->select(':card.card_id, artist.artist_id, artist.name')
				->fetchAll() as $art) {
			$artists[$art->card_id] = $art;
		}
		$grido->addColumn('artist', 'A')
				->setCustomRender(function ($item) use ($artistLink, $artists) {
					return isset($artists[$item->card_id])
							? \Nette\Utils\Html::el('a')
								->href($artistLink->setParameter('id', $artists[$item->card_id]->artist_id))
								->setText(\Nette\Utils\Strings::truncate($artists[$item->card_id]->name, 25))
							: '';
				});
		
		$grido->addColumn('ready', '')
				->setCustomRender(function ($item) {
					return $item->ready ? '<i class="icon-ok"></i>' : '';
				});
				
		$editions = array();
		foreach ($this->game->getEditions()->fetchAll() as $edition) {
			$editions[$edition->edition_id] = $this->translator->translate('edition.' . $edition->edition_id);
		}
		$grido->addFilterCustom('edition_id', new \Framework\Forms\Controls\MultiOptionList('Expanze', $editions))
				->setCondition(\Grido\Components\Filters\Filter::CONDITION_CALLBACK, function ($item) {
					return array('edition_id IN %i', $item);
				});;

		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->card_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat kartu '" . $self->translator->translate('card.' . $item->card_id) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteCard()
	{
		$this->getPresenter()->flashMessage('Karta byla smazána.', 'success');
		$this->redirect('this');
	}
}
