<?php

namespace App\CoraabiaModule;

use Framework;


class DeckControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Coraabia @inject */
	public $coraabia;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentDecklist($name)
	{
		$self = $this;
		$exportLink = $this->lazyLink('exportBotDeck');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->coraabia->getDecks()))
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
				->setFilterText();
		
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
	
	
	/**
	 * @todo
	 * @param mixed $deck 
	 */
	protected function exportBotDeck($deck)
	{
		/*if ($deck) {
			$coraabia = $this->coraabiaFactory->access();
			
			$cards = $coraabia->getDeckInstances()->select('instance.card_id')
					->where('deck_id = ?', $deck->deck_id)
					->fetchAll();
			$connections = $coraabia->getDeckConnections()->select('connection_id')
					->where('deck_id = ?', $deck->deck_id)
					->fetchAll();
			
			try {
				$this->game->createBotDeck($deck->toArray(), $cards, $connections);
				$this->getPresenter()->flashMessage("Balík '{$deck->deck_id}' byl exportován na dev.", 'success');
			} catch (\Exception $e) {
				$this->getPresenter()->flashMessage($e->getMessage(), 'error');
			}

		} else {
			$this->getPresenter()->flashMessage("Balík není bot balík a nelze ho exportovat.", 'error');
		}*/
	}
	
	
	/**
	 * @todo
	 */
	public function handleExportBotDeck()
	{
		/*$coraabia = $this->coraabiaFactory->access();
		
		$deckId = (int)$this->getParameter('id');
		$deck = $coraabia->getDecks()->where('deck.deck_id = ?', $deckId)
				->where('user.username ~ ?', '^b0t[1-9][0-9]*$')
				->fetch();
		
		$this->exportBotDeck($deck);*/
	}
	
	
	/**
	 * @todo
	 */
	public function handleExportBotDecks()
	{
		/*$coraabia = $this->coraabiaFactory->access();
		
		$decks = $coraabia->getDecks()->where('user.username ~ ?', '^b0t[1-9][0-9]*$')
				->fetchAll();
		
		if ($decks) {
			
			$this->game->deleteBotDecks();
			
			foreach ($decks as $deck) {
				$this->exportBotDeck($deck);
			}
		}
		
		$this->redirect('this');*/
	}
}
