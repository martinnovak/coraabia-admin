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
		
	
	
	public function renderBazaar()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/bazaar.latte');
		$template->render();
	}
	
	
	public function createComponentBazaar($name)
	{
		$self = $this;
		
		//request
		$request = $this->mapiRequestFactory->create(array('id' => 'transactions'), 'txs');
		
		//types
		$tmp = array_values(array_unique(array_map(function ($item) {
			return $item->type;
		}, $request->load())));
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
				->setFilterRenderType(Filter::RENDER_OUTER);
		
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
				->setCustomHref(function ($item) use ($self) {
					return $self->getPresenter()->lazyLink('showViewTransaction', array('id' => $item->txId));
				});
		
		return $grido;
	}
	
	
	
	public function renderShowTransaction()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/transaction.latte');
		
		$id = $this->getPresenter()->getParameter('id');
		$transactions = array_filter($this->mapiRequestFactory->create(array('id' => 'transactions'), 'txs')->load(), function ($item) use ($id) {
			return $item->txId == $id;
		});
		$transaction = array_pop($transactions);
		$transaction->node = json_decode($transaction->node);
		$template->transaction = $transaction;
		
		$template->render();
	}
}
