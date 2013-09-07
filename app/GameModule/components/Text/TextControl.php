<?php

namespace App\GameModule;

use Nette,
	Framework;


/**
 * @method setKey(string) 
 */
class TextControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Nette\Caching\IStorage @inject */
	public $storage;
	
	/** @var string */
	private $key;
	
	
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
	public function createComponentTextlist($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('editStaticText');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->game->getStaticTexts())
				->setPrimaryKey('key')
				->setDefaultSort(array('key' => 'ASC'));
		
		$grido->addColumn('key', 'Klíč')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->key) . '">' . $item->key . '</a>';
				})
				->setFilterText()
						->setSuggestion(function ($item) { //no idea why it bugs out when you use ->setSuggestion(NULL)
							return $item->key;
						});
		
		$grido->addColumn('value', 'Hodnota')
				->setSortable()
				->setFilterText()
						->setSuggestion();
		
		$grido->addColumn('valid', 'Překlady')
				->setCustomRender(function ($item) use ($self) {
					$result = '';
					foreach ($self->locales->langs as $lang) {
						if ($self->translator->getTranslation($item->key, $lang) == '') { //intentionaly ==
							$result .= '<div class="blink"><i class="icon-warning-sign"></i>&nbsp;' . strtoupper($lang) . '</div>';
						}
					}
					return $result;
				});
				
		return $grido;
	}
	
	
	public function renderEdit()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/textForm.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentTextForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$defaults = array();
		foreach ($this->locales->langs as $lang) {
			
			$form->addGroup(strtoupper($lang));
			
			$form->addTextArea('value_' . $lang, 'Text')
				->setAttribute('rows', 15);
			
			$defaults['value_' . $lang] = $this->translator->getTranslation($this->key, $lang);
		}
		
		$form->setCurrentGroup();
		
		$form->addSubmit('submit', 'Změnit');
		
		$form->setDefaults($defaults);		
		$form->onSuccess[] = $this->textFormSuccess;
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form 
	 */
	public function textFormSuccess(Nette\Application\UI\Form $form)
	{
		try {
			$this->game->getConnection()->beginTransaction();
			foreach ($this->locales->langs as $lang) {
				$value = 'value_' . $lang;
				$this->game->updateStaticText($this->key, $lang, $form->getValues()->$value);
			}
			$this->game->getConnection()->commit();
			$this->getPresenter()->flashMessage('Text byl uložen.', 'success');
		} catch (\Exception $e) {
			$this->game->getConnection()->rollBack();
			$form->addError($e->getMessage());
		}
		
		$cache = new Nette\Caching\Cache($this->storage, str_replace('\\', '.', get_class($this->translator)));
		$cache->remove('translations');
	}
}
