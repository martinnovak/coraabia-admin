<?php

namespace App;

use Nette,
	Framework,
	Grido,
	Grido\Components\Filters\Filter;



class AuditControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\API @inject */
	public $api;
		
	
	
	public function renderBazaar()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/bazaar.latte');
		$template->render();
	}
	
	
	public function createComponentBazaar($name)
	{
		$self = $this;
		
		$transactions = array_map(function ($item) {
			$item->node = json_encode($item->node);
			return (array)$item;
		}, $this->api->query(array('id' => 'transactions'))->txs);
		
		$tmp = array_values(array_unique(array_map(function ($item) {
			return $item['type'];
		}, $transactions)));
		array_unshift($tmp, '');
		$types = array_combine($tmp, $tmp);
		
		$grido = new Grido\Grid($this, $name);
		$grido->setModel($transactions)
				->setDefaultPerPage(1000)
				->setPerPageList(array(100, 200, 500, 1000))
				->setTranslator($this->translator)
				->setPrimaryKey('txId')
				->setDefaultSort(array('txId' => 'asc'))
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
					return date('d.m.Y H:i:s', $item['timestamp']);
				});
		
		$grido->addColumn('node', 'Data')
				->setSortable()
				->setTruncate(100)
				->setFilter();
		
		$grido->addAction('show', 'Podrobnosti')
				->setIcon('list')
				->setCustomHref(function ($item) use ($self) {
					return $self->getPresenter()->lazyLink('showViewTransaction', array('id' => $item['txId']));
				});
		
		return $grido;
	}
	
	
	
	public function renderShowTransaction()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/transaction.latte');
		
		$id = $this->getPresenter()->getParameter('id');
		$transactions = array_filter($this->api->query(array('id' => 'transactions'))->txs, function ($item) use ($id) {
			return $item->txId == $id;
		});
		$template->transaction = array_pop($transactions);
		
		$template->render();
	}
}
