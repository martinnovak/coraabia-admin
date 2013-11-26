<?php

namespace App\GameModule;

use Nette,
	Framework;


/**
 * @method setKey(string) 
 */
class TextControl extends Framework\Application\UI\BaseControl
{
	const PREFIX = 'game-text.';
	
	/** @var \Model\Game @inject */
	public $game;
		
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Model\Translator @inject */
	public $translator;
	
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
		$editLink = $this->getPresenter()->lazyLink('editGameText');
		$removeLink = $this->lazyLink('deleteGameText');
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->game->getGameTexts()))
				->setPrimaryKey('key')
				->setDefaultSort(array('key' => 'ASC'));
		
		$grido->addColumn('key', 'Klíč')
				->setSortable()
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', substr($item->key, strlen($self::PREFIX))) . '">' . substr($item->key, strlen($self::PREFIX)) . '</a>';
				})
				->setFilterText()
						->setCondition(\Grido\Components\Filters\Filter::CONDITION_CALLBACK, function ($value) use ($self) {
							return array('[key] = %s', $self::PREFIX . $value);
						});
		
		$grido->addColumn('value', 'Hodnota')
				->setSortable()
				->setFilterText();
		
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
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($self, $removeLink) {
					return $removeLink->setParameter('id', substr($item->key, strlen($self::PREFIX)));
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat text '" . substr($item->key, strlen($self::PREFIX)) . "'?";
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
			
			$defaults['value_' . $lang] = $this->translator->getTranslation(self::PREFIX . $this->key, $lang);
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
		$values = $form->getValues();
		
		try {
			$texts = array();
			foreach ($this->locales->langs as $lang) {
				$value = 'value_' . $lang;
				$texts[] = array(
					'key' => self::PREFIX . $this->key,
					'lang' => $lang,
					'value' => $values->$value
				);
			}
			$this->game->updateGameTexts($texts);
			$this->getPresenter()->flashMessage('Text byl uložen.', 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($this->translator instanceof Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
	}
	
	
	public function renderAdd()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/add.latte');
		$template->render();
	}
	
	
	public function createComponentAddTextForm($name)
	{
		$form = $this->formFactory->create($this, $name);
		
		$form->addText('key', 'Klíč')
				->setRequired();
		
		foreach ($this->locales->langs as $lang) {
			
			$form->addGroup(strtoupper($lang));
			
			$form->addTextArea('value_' . $lang, 'Text')
				->setAttribute('rows', 15);
		}
		
		$form->setCurrentGroup();
		
		$form->addSubmit('submit', 'Vytvořit');
		
		$form->onSuccess[] = $this->addTextFormSuccess;
		
		return $form;
	}
	
	
	public function addTextFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$saved = FALSE;
		try {
			$texts = array();
			foreach ($this->locales->langs as $lang) {
				$value = 'value_' . $lang;
				$texts[] = array(
					'key' => self::PREFIX . $values->key,
					'lang' => $lang,
					'value' => $values->$value
				);
			}
			$this->game->createGameTexts($texts);
			$this->getPresenter()->flashMessage('Text byl uložen.', 'success');
			$saved = TRUE;
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
		}
		
		if ($saved) {
			if ($this->translator instanceof Framework\Localization\ICachingTranslator) {
				$this->translator->clean();
			}
			
			$this->getPresenter()->redirect('Text:editGameText', array('id' => $values->key));
		}
	}
	
	
	public function handleDeleteGameText()
	{
		$key = self::PREFIX . $this->getParameter('id');
		try {
			$this->game->deleteGameText($key);
			$this->getPresenter()->flashMessage('Text byl smazán.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage('Text se nepodařilo smazat.', 'error');
		}
		
		$this->redirect('this');
	}
}
