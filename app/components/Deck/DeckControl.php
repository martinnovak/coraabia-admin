<?php

namespace App;

use Nette,
	Framework,
	Model,
	Grido;



class DeckControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Model\CoraabiaFactory @inject */
	public $coraabiaFactory;
	
	
	
	public function renderList()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->hookManager->listen('sidebar', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate()->setFile(__DIR__ . '/listSidebar.latte');
			$hook->addTemplate($tmpl);
		});
		$template->render();
	}
	
	
	
	public function createComponentDecklist($name)
	{
		$exportLink = $this->lazyLink('exportBotDeck');
		
		$grido = new Grido\Grid($this, $name);
		$grido->setModel($this->coraabiaFactory->access()->decks)
				->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setPrimaryKey('deck_id')
				->setDefaultSort(array('name' => 'ASC'));
		
		$grido->addColumn('deck_id', 'ID')
				->setSortable();
		
		$grido->addColumn('user_id', 'UID')
				->setSortable();
		
		$grido->addColumn('username', 'Uživatel')
				->setSortable();
		
		$grido->addColumn('name', 'Jméno')
				->setSortable();
		
		$grido->addAction('export', 'Export')
				->setIcon('share-alt')
				->setCustomHref(function ($item) use ($exportLink) {
					return $exportLink->setParameter('id', $item->deck_id);
				})
				->setDisable(function ($item) {
					return !preg_match('/^b0t[1-9][0-9]*$/i', $item->username);
				});
		
		return $grido;
	}
	
	
	
	public function handleexportBotDeck()
	{
		$deck = Model\Deck::from($this->coraabiaFactory->access()->decks->where('d.deck_id = %i', $this->getParameter('id'))->fetch()->toArray());
		$deck->instances = array_map(function ($item) {
			return \Model\Instance::from($item->toArray());
		}, $this->coraabiaFactory->access()->deckInstances->where('di.deck_id = %i', $this->getParameter('id'))->fetchAll());
		
		$gameDeck = \Model\BotGameDeck::fromDeck($deck);
		//$this->game->gameDeck = $gameDeck;
	}
}
