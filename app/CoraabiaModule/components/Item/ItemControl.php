<?php

namespace App\CoraabiaModule;

use Framework,
	Nette;


class ItemControl extends Framework\Application\UI\BaseControl
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
	
	
	public function createComponentItemList($name)
	{
		$self = $this;
		$baseUri = $this->template->baseUri;
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\MapiDataSource($this->bazaar->getShopItems()))
				->setPrimaryKey('itemId')
				->setDefaultSort(array('itemId' => 'DESC'))
				->setPropertyAccessor(new Framework\Grido\PropertyAccessors\MapiPropertyAccessor);
		
		$grido->addColumn('itemId', 'ID')
				->setSortable();
		
		$grido->addColumn('type', 'T')
				->setCustomRender(function ($item) use ($self, $baseUri) {
					switch ($item->type) {
						case 'CARD':
							$result = Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/card.png")
								. '&nbsp;'
								. $self->translator->translate('card.' . $item->customId);
							break;
						default:
							$result = $item->type . ' ' . $item->customId;
					}
					return $result;
				});
				
		$grido->addColumn('validFrom', 'Od')
				->setCustomRender(function ($item) {
					return isset($item->validFrom) ? Nette\DateTime::from($item->validFrom / 1000) : '';
				});
				
		$grido->addColumn('validTo', 'Do')
				->setCustomRender(function ($item) {
					return isset($item->validTo) ? Nette\DateTime::from($item->validTo / 1000) : '';
				});
				
		$grido->addColumn('valid', '')
				->setCustomRender(function ($item) {
					return $item->valid ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addColumn('offers', 'Nabídek')
				->setCustomRender(function ($item) {
					return isset($item->offers) ? count($item->offers) : 0;
				});
				
		$grido->addColumn('script', '')
				->setCustomRender(function ($item) {
					return isset($item->script) ? $item->script : '';
				});
		
		return $grido;
	}
}
