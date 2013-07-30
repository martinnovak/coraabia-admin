<?php

namespace App;

use Nette,
	Framework,
	Model,
	Grido;



/**
 * @method setKey(string) 
 */
class TextControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var string */
	private $key;
	
	
	
	public function renderList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	
	public function createComponentTextlist($name)
	{
		$editLink = $this->presenter->lazyLink('updateStatic');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->game->staticTexts)
				->setPrimaryKey('key')
				->setDefaultSort(array('key' => 'ASC'));
		
		$grido->addColumn('key', 'Klíč')
				->setSortable()
				->setFilterText()
						->setSuggestion(function ($item) { //no idea why it bugs out when you use ->setSuggestion(NULL)
							return $item->key;
						});
		
		$grido->addColumn('value', 'Hodnota')
				->setSortable()
				->setFilterText()
						->setSuggestion();
		
		$grido->addAction('edit', 'Změnit')
				->setIcon('edit')
				->setCustomHref(function ($item) use ($editLink) {
					return $editLink->setParameter('id', $item->key);
				});
		
		return $grido;
	}
	
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/edit.latte');
		$template->render();
	}
	
	
	
	public function createComponentTextEditForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addTextArea('value', 'Text')
				->setAttribute('rows', 15);
		
		$form->addSubmit('submit', 'Změnit');
		
		$form->setDefaults($this->game->staticTexts->where('key = ?', $this->key)->fetch()->toArray());		
		$form->onSuccess[] = $this->textEditFormSuccess;
		return $form;
	}
	
	
	
	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function textEditFormSuccess($form)
	{
		try {
			$this->game->updateStaticText($this->key, $form->getValues()->value);
			$this->presenter->flashMessage('Text byl uložen.', 'success');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
	}
}
