<?php

namespace App;

use Nette,
	Framework,
	Grido,
	Grido\Components\Filters\Filter;



class AuditControl extends Framework\Application\UI\BaseControl
{
	/** @var \Coraabia\Mapi\MapiRequestFactory @inject */
	public $mapiRequestFactory;
	
	/** @var \Model\Game @inject */
	public $game;
		
	
	
	public function renderBazaar()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/bazaar.latte');
		$template->render();
	}
	
	
	public function createComponentBazaar($name)
	{
		$link = $this->getPresenter()->lazyLink('showViewTransaction');
		
		//request
		$request = $this->mapiRequestFactory->create('transactions', 'txs');
		
		//types
		$tmp = $this->game->bazaarTransactionTypes;
		array_unshift($tmp, '');
		$types = array_combine($tmp, $tmp);
		
		//grido
		$grido = new Grido\Grid($this, $name);
		$grido->setModel(new \Coraabia\Mapi\MapiDataSource($request))
				->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setPrimaryKey('txId')
				->setDefaultSort(array('txId' => 'ASC'))
				->setFilterRenderType(Filter::RENDER_OUTER)
				->setPropertyAccessor(new \Coraabia\Mapi\MapiPropertyAccessor);
		
		$grido->addColumn('txId', 'ID')
				->setSortable();
		
		$grido->addColumn('userId', 'UID')
				->setSortable()
				->setFilter(Filter::TYPE_NUMBER);
		
		$grido->addColumn('name', 'Uživatel')
				->setSortable()
				->setFilter();
				
		$grido->addColumn('type', 'Typ')
				->setSortable()
				->setFilter(Filter::TYPE_SELECT, $types);

		$grido->addColumn('timestamp', 'Čas')
				->setSortable()
				->setCustomRender(function ($item) {
					return date('d.m.Y H:i:s', $item->timestamp);
				});
		
		$grido->addColumn('node', 'Data')
				->setTruncate(80)
				->setFilter();
		
		$grido->addAction('show', 'Podrobnosti')
				->setIcon('list')
				->setCustomHref(function ($item) use ($link) {
					return $link->setParameter('id', $item->txId);
				});
		
		return $grido;
	}
	
	
	
	public function renderShowTransaction()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/transaction.latte');
		
		$id = $this->getPresenter()->getParameter('id');
		$transactions = array_filter($this->mapiRequestFactory->create('transactions', 'txs')->load(), function ($item) use ($id) {
			return $item->txId == $id;
		});
		$transaction = array_pop($transactions);
		$template->transaction = $transaction;
		
		$template->render();
	}
}
