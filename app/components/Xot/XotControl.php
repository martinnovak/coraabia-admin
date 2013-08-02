<?php

namespace App;

use Nette,
	Framework,
	Model,
	Grido,
	Mapi;



class XotControl extends Framework\Application\UI\BaseControl
{
	/** @var \Framework\Mapi\MapiRequestFactory @inject */
	public $mapiRequestFactory;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	
	public function createComponentXotlist($name)
	{
		$self = $this;
		$editLink = $this->presenter->lazyLink('doBazaarEditRefill');
		$request = $this->mapiRequestFactory->create('all-refill-offers', 'refillOffers');
		
		$grido = $this->gridoFactory->create($this, $name);
		
		$grido->setModel(new Framework\Mapi\MapiDataSource($request))
				->setPrimaryKey('refillOfferId')
				->setPropertyAccessor(new Framework\Mapi\MapiPropertyAccessor);
		
		$grido->addColumnText('title', 'Název')
				->setCustomRender(function ($item) use ($self) {
					return (string)$item->titles->{$self->locales->lang};
				});
				
		$grido->addColumnText('price', 'Cena')
				->setCustomRender(function ($item) use ($self) {
					return $item->price . ' ' . $item->currencyExt;
				});
				
		$grido->addColumnText('currency', 'Měna')
				->setCustomRender(function ($item) use ($self) {
					return $item->amount . ' ' . $item->currencyInt;
				});
				
		$grido->addColumn('valid', 'Aktivní')
				->setCustomRender(function ($item) use ($self) {
					return $item->valid ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addAction('edit', 'Změnit')
				->setIcon('edit')
				->setCustomHref(function ($item) use ($editLink) {
					return $editLink->setParameter('id', $item->refillOfferId);
				});
		
		return $grido;
	}
}
