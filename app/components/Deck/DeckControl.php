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
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	
	
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
		$self = $this;
		$exportLink = $this->lazyLink('exportBotDeck');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->coraabiaFactory->access()->decks)
				->setPrimaryKey('deck_id')
				->setDefaultSort(array('name' => 'ASC'));
		
		$grido->addColumn('deck_id', 'ID')
				->setSortable();
		
		$grido->addColumn('user_id', 'UID')
				->setSortable()
				->setColumn('deck.user_id')
				->setCustomRender(function ($item) {
					return $item->user_id;
				})
				->setFilterText()
						->setColumn('deck.user_id')
						->setSuggestion(function ($item) {
							return $item->user_id;
						});
		
		$grido->addColumn('username', 'Uživatel')
				->setSortable()
				->setColumn('user.username')
				->setCustomRender(function ($item) {
					return $item->user->username;
				})
				->setFilterText()
						->setColumn('user.username')
						->setSuggestion(function ($item) {
							return $item->user->username;
						});
		
		$grido->addColumn('name', 'Jméno')
				->setSortable();
		
		$grido->addAction('export', 'Export')
				->setIcon('share-alt')
				->setCustomHref(function ($item) use ($exportLink) {
					return $exportLink->setParameter('id', $item->deck_id);
				})
				->setDisable(function ($item) use ($self) {
					return !(preg_match('/^b0t[1-9][0-9]*$/i', $item->username) && $self->locales->server == \Coraabia\ServerEnum::STAGE);
				});
		
		return $grido;
	}
	
	
	
	protected function exportBotDeck($deck)
	{
		if ($deck) {
			$coraabia = $this->coraabiaFactory->access();
			
			$cards = $coraabia->deckInstances->select('instance.card_id')
					->where('deck_id = ?', $deck->deck_id)
					->fetchAll();
			$connections = $coraabia->deckConnections->select('connection_id')
					->where('deck_id = ?', $deck->deck_id)
					->fetchAll();
			
			try {
				$this->game->createBotDeck($deck->toArray(), $cards, $connections);
				$this->presenter->flashMessage("Balík '{$deck->deck_id}' byl exportován na dev.", 'success');
			} catch (\Exception $e) {
				$this->presenter->flashMessage($e->getMessage(), 'error');
			}

		} else {
			$this->presenter->flashMessage("Balík není bot balík a nelze ho exportovat.", 'error');
		}
	}
	
	
	
	public function handleExportBotDeck()
	{
		$coraabia = $this->coraabiaFactory->access();
		
		$deckId = (int)$this->getParameter('id');
		$deck = $coraabia->decks->where('deck.deck_id = ?', $deckId)
				->where('user.username ~ ?', '^b0t[1-9][0-9]*$')
				->fetch();
		
		$this->exportBotDeck($deck);
	}
	
	
	
	public function handleExportBotDecks()
	{
		$coraabia = $this->coraabiaFactory->access();
		
		$decks = $coraabia->decks->where('user.username ~ ?', '^b0t[1-9][0-9]*$')
				->fetchAll();
		
		if ($decks) {
			
			$this->game->deleteBotDecks();
			
			foreach ($decks as $deck) {
				$this->exportBotDeck($deck);
			}
		}
		
		$this->redirect('this');
	}
}
