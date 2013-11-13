<?php

namespace App\CoraabiaModule;

use Framework,
	Nette;


/**
 * @method setItemId(int)
 */
class ItemControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Bazaar @inject */
	public $bazaar;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var int */
	private $itemId;
	
	
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
		$editLink = $this->getPresenter()->lazyLink('editItem');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\MapiDataSource($this->bazaar->getShopItems()))
				->setPrimaryKey('itemId')
				->setDefaultSort(array('itemId' => 'DESC'))
				->setPropertyAccessor(new Framework\Grido\PropertyAccessors\MapiPropertyAccessor);
		
		$grido->addColumn('itemId', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return Nette\Utils\Html::el('a')
							->href($editLink->setParameter('id', $item->itemId))
							->setText($item->itemId);
				});
		
		$grido->addColumn('type', 'T')
				->setCustomRender(function ($item) use ($self, $baseUri, $editLink) {
					switch ($item->type) {
						case 'CARD':
							$result = Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/card.png")
								. '&nbsp;'
								. Nette\Utils\Html::el('a')
									->href($editLink->setParameter('id', $item->itemId))
									->setText($self->translator->translate('card.' . $item->customId));
							break;
						default:
							$result = Nette\Utils\Html::el('a')
										->href($editLink->setParameter('id', $item->itemId))
										->setText($item->type . ' ' . $item->customId);
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
					return Nette\Utils\Strings::truncate(isset($item->script) ? $item->script : '', 80);
				});
		
		return $grido;
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		$template->render();
	}
	
	
	public function createComponentItemForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addGroup('Položka');
		
		$form->addText('name', 'Jméno')
				->setDisabled()
				->setOmitted();
		
		$form->addText('validFrom', 'Od'); //@todo rules
		
		$form->addText('validTo', 'Do'); //@todo rules
		
		$form->addTextArea('script', 'Skript')
				->setAttribute('rows', 15);
		
		$form->setCurrentGroup();
		$form->addSubmit('submit', 'Uložit');
		
		$form->onSuccess[] = $this->itemFormSuccess;
		
		try {
			if ($this->itemId !== NULL) {
				$item = $this->bazaar->getShopItems()
						->setParam('findItemFilter', array(
							'itemId' => array($this->itemId),
							'includeShopOffers' => TRUE,
							'includeMarketOffers' => FALSE
						))
						->load();
				if (empty($item) || count($item) > 1) {
					throw new \Exception("Položka '{$this->itemId}' nebyla nalezena.");
				} else {
					$item = $item[0];
				}
				if (isset($item->validFrom)) {
					$item->validFrom = Nette\DateTime::from($item->validFrom / 1000);
				}
				if (isset($item->validTo)) {
					$item->validTo = Nette\DateTime::from($item->validTo / 1000);
				}
				$item->name = $this->getItemName((array)$item);
				$form->setDefaults((array)$item);
			}
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		return $form;
	}
	
	
	public function itemFormSuccess(\Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		if ($this->itemId !== NULL) {
			try {
				$item = $this->bazaar->getShopItems()
						->setParam('findItemFilter', array(
							'itemId' => array($this->itemId),
							'includeShopOffers' => TRUE,
							'includeMarketOffers' => FALSE
						))
						->load();
				if (empty($item) || count($item) > 1) {
					throw new \Exception("Položka '{$this->itemId}' nebyla nalezena.");
				} else {
					$item = $item[0];
				}
				
				if (!empty($values->validFrom)) {
					$item->validFrom = 1000 * strtotime($values->validFrom);
				} else {
					unset($item->validFrom);
				}
				if (!empty($values->validTo)) {
					$item->validTo = 1000 * strtotime($values->validTo);
				} else {
					unset($item->validTo);
				}
				$item->script = $values->script;
				
				$this->bazaar->saveItem((array)$item);
				$this->getPresenter()->flashMessage("Položka '{$this->itemId}' byla uložena.", 'success');
			} catch (\Exception $e) {
				$form->addError($e->getMessage());
			}
			
			$this->redirect('this');
		}
	}
	
	
	/**
	 * @param array $item
	 * @return string
	 */
	public function getItemName(array $item)
	{
		if ($item['type'] == 'CARD') {
			return $this->translator->translate('card.' . $item['customId']);
		} else {
			return $item['type'] . ' ' . $item['customId'];
		}
	}
}
