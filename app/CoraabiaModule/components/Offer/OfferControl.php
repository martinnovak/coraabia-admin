<?php

namespace App\CoraabiaModule;

use Framework;


class OfferControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Bazaar @inject */
	public $bazaar;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	public function createComponentOfferList($name)
	{
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\MapiDataSource($this->bazaar->getShopOffers()))
				->setPrimaryKey('offerId')
				->setDefaultSort(array('created' => 'DESC'))
				->setPropertyAccessor(new Framework\Grido\PropertyAccessors\MapiPropertyAccessor);
		
		$grido->addColumn('offerId', 'ID')
				->setSortable();
		
		$grido->addColumn('created', 'Vytvořeno')
				->setSortable();
		
		return $grido;
	}
}