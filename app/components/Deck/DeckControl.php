<?php

namespace App;

use Nette,
	Framework,
	Grido;



class DeckControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	
	public function createComponentDecklist($name)
	{
		$exportLink = $this->lazyLink('exportBotDeck');
		$coraabia = $this->getPresenter()->context->getService($this->locales->server);
		
		$grido = new Grido\Grid($this, $name);
		$grido->setModel($coraabia->decks)
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
		
	}
}
