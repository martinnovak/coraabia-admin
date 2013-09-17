<?php

namespace App\GameModule;

use Framework,
	Nette;


/**
 * @method setActivityId(string)
 */
class ActivityControl extends Framework\Application\UI\BaseControl
{
	const VAR_VISIBLE_DEFAULT = '1';
	const VAR_PLAYABLE_DEFAULT = '0';
	
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Framework\Application\FormFactory @inject */
	public $formFactory;
	
	/** @var \Framework\Kapafaa\KapafaaParser @inject */
	public $kapafaaParser;
	
	/** @var \Nette\Caching\IStorage @inject */
	public $storage;
	
	/** @var string */
	private $activityId;
	
	
	public function renderActivityList()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/list.latte');
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Grido\Grid 
	 */
	public function createComponentActivityList($name)
	{
		$self = $this;
		$editLink = $this->getPresenter()->lazyLink('editActivity');
		$removeLink = $this->lazyLink('deleteActivity');
		$baseUri = $this->template->baseUri;
		
		$grido = $this->gridoFactory->create($this, $name);
		$grido->setModel($this->game->getActivities())
				->setPrimaryKey('activity_id')
				->setDefaultSort(array('fraction' => 'ASC', 'activity_id' => 'ASC'));
		
		$grido->addColumnNumber('activity_id', 'ID')
				->setSortable();
		
		$grido->addColumn('translated_name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->activity_id) . '" class="' . strtolower($item->fraction) . '">' . trim($self->translator->translate('activity-name.' . $item->activity_id)) . '</a>';
				});
				
		$grido->addColumn('fraction', 'F')
				->setSortable()
				->setCustomRender(function ($item) use ($baseUri) {
					return \Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/" .
							($item->fraction ? strtolower($item->fraction) : 'card') .
							".png");
				});
				
		$grido->addColumn('bot_id', 'Bot')
				->setSortable()
				->setCustomRender(function ($item) {
					return $item->bot_id ? ('<i class="icon-user"></i>&nbsp;' . $item->bot->name) : '';
				});
		
		$grido->addColumn('ready', '')
				->setCustomRender(function ($item) {
					return $item->ready ? '<i class="icon-ok"></i>' : '';
				});
				
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->activity_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat aktivitu '" . $self->translator->translate('activity-name.' . $item->activity_id) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteActivity()
	{
		$this->getPresenter()->flashMessage('Aktivita byla smazána.', 'success');
	}
	
	
	public function renderCreateActivity()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/createActivity.latte');
		$this->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/activityScripts.latte');
			
			$tmpl->activityData = array(
				'gamerooms' => array_values(array_map(function ($item) use ($self) {
					return array(
						'id' => $item->gameroom_id,
						'name' => $self->translator->translate('gameroom.' . $item->gameroom_id),
						'ready' => $item->ready,
						'ag_ready' => FALSE
					);
				}, $self->game->getGamerooms()->fetchAll())),
				'activities' => array_values(array_map(function ($item) use ($self) {
					return array(
						'id' => $item->activity_id,
						'name' => $self->translator->translate('activity-name.' . $item->activity_id),
						'ready' => $item->ready
					);
				}, $self->game->getActivities()->fetchAll()))
			);
			
			//@todo cache
			$kapafaaDefinitions = array();
			foreach ($self->kapafaaParser->loadClassData()->classes as $class => $data) {
				$def = array(
					'name' => $self->translator->translate($data['description']),
					'type' => $class,
					'kapafaa' => $data['kapafaa'],
					'parent' => $data['parent']
				);
				preg_match_all('/%([a-z]+)%/i', $data['kapafaa'], $matches);
				$params = array();
				for ($i = 0; $i < count($matches[1]); $i++) {
					$params[] = array(
						'name' => $matches[1][$i],
						'type' => $data['params'][$i],
						'value' => NULL
					);
				}
				$def['params'] = $params;
				$kapafaaDefinitions[] = $def;
			}
			$tmpl->kapafaaDefinitions = $kapafaaDefinitions;
			
			$hook->addTemplate($tmpl);
		});
		
		/* DEBUG */
		/*$this->kapafaaParser->loadClassData();
		Framework\Diagnostics\TimerPanel::timer('parse');
		$scripts = $this->kapafaaParser->parse("(\ntrigger.gameplay.me.autowin\neff.gameplay(me.duelWin += 1, multiply.cardInDeck)\n)");
		Framework\Diagnostics\TimerPanel::timer('parse');
		Nette\Diagnostics\Debugger::dump($scripts[0]->objects);
		Nette\Diagnostics\Debugger::dump((string)$scripts[0]);*/
		/* DEBUG */
		
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\ComponentModel\IComponent
	 */
	public function createComponentActivityCreateForm($name)
	{
		$self = $this;
		$form = $this->formFactory->create($this, $name);

		$form->addGroup('Nastavení');
			
		$form->addText('activity_id', 'ID')
			->setRequired('Vyplňte ID aktivity.')
			->addRule(Nette\Forms\Form::MAX_LENGTH, 'Maximální délka ID je %value znaků.', 20)
			->addRule(Nette\Forms\Form::PATTERN, 'ID musí končit jednou z přípon _I, _II, _III, _IV a nesmí obsahovat pomlčku', '[^-]+_I([IV]?|II)');
		
		$fractions = $this->game->getFractions();
		$fractions = array_combine(
				array_merge(array(''), $fractions),
				array_merge(array(''), array_map(function ($item) use ($self) {
					return $self->translator->translate('fraction.' . $item);
				}, $fractions))
		);
		$form->addSelect('fraction', 'Frakce', $fractions);
		
		$variantTypes = $this->game->getActivityVariants();
		$variantTypes = array_combine($variantTypes, $variantTypes);
		$form->addSelect('variant_type', 'Varianta', $variantTypes)
			->setRequired();
		
		$activityTypes = $this->game->getActivityTypes();
		$activityTypes = array_combine($activityTypes, $activityTypes);
		$form->addSelect('activity_type', 'Typ aktivity', $activityTypes)
			->setRequired();
		
		$activityStartTypes = $this->game->getActivityStartTypes();
		$activityStartTypes = array_combine($activityStartTypes, $activityStartTypes);
		$form->addSelect('start_type', 'Typ startu', $activityStartTypes)
			->setRequired();
		
		$form->addGroup('Pozice');
		
		$form->addText('tree', 'Strom')
			->setRequired('Vyplňte strom.')
			->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
			->setOption('description', 'Stromy 0-5 jsou frakční a obecný.');
		
		$form->addText('posx', 'Pozice X')
			->setRequired('Vyplňte x-ovou pozici ve stromě.')
			->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
			->setOption('description', '0 je hlavní linka stromu.');
		
		$form->addText('posy', 'Pozice Y')
			->setRequired('Vyplňte y-ovou pozici ve stromě.')
			->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
			->setOption('description', '0 je kořen stromu.');
		
		foreach ($this->locales->langs as $lang) {
			
			$form->addGroup(strtoupper($lang));
			
			$form->addText('activity_name_' . $lang, 'Jméno')
				->setRequired('Vyplňte jméno ' . strtoupper($lang) . ' aktivity');
			
			$form->addTextArea('activity_flavor_' . $lang, 'Flavor')
				->setAttribute('rows', 10);
			
			$form->addTextArea('activity_task_' . $lang, 'Text zadání')
				->setAttribute('rows', 10);
			
			$form->addTextArea('activity_finish_' . $lang, 'Text splnění')
				->setAttribute('rows', 10);
		}

		$form->setCurrentGroup();
		
		$form->addHidden('gameroomList')
			->getControlPrototype()
			->addAttributes(array('data-bind' => 'value: gameroomList()'));
		$form->addHidden('parentList')
			->getControlPrototype()
			->addAttributes(array('data-bind' => 'value: parentList()'));
		
		$form->addSubmit('submit', 'Uložit');
		
		$form->onSuccess[] = $this->activityCreateFormSuccess;
		return $form;
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function activityCreateFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$row = NULL;
		try {
			$this->game->getSource()->beginTransaction();
			
			//aktivita
			//proměnné, filtry, observry a jejich napojení
			//rodičovské aktivity orig vs new
			//\Nette\Diagnostics\Debugger::dump($this->game->getParentActivities($values->activity_id));
			//$this->updateActivityGamerooms($values->activity_id, explode('--', $values->gameroomList), array());
			//$this->createActivityTexts($values->activity_id, $values);
			
			$this->game->getSource()->commit();
		} catch (\Exception $e) {
			$this->game->getSource()->rollBack();
			$form->addError($e->getMessage());
		}
		
		if ($row) {
			$this->getPresenter()->redirect('Activity:editActivity', array('id' => $row->activity_id));
		}
	}
	
	
	protected function createActivityTexts($activityId, array $values)
	{
		$keys = array(
			'activity_name',
			'activity_flavor',
			'activity_task',
			'activity_finish'
		);
		foreach ($values as $key => $value) {
			if (preg_match('/^(' . implode('|', $keys) . ')_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
				$this->game->getTranslations()->insert(array(
					'key' => str_replace('_', '-', $matches[1]) . '.' . $activityId,
					'lang' => $matches[2],
					'value' => $value
				));
			}
		}
	}
	
	
	/**
	 * @todo call model, not directly
	 * @todo optimize?
	 */
	protected function updateActivityGamerooms($activityId, array $new, array $original)
	{
		$toRemove = array_diff($original, $new);
		$toAdd = array_diff($new, $original);
		
		//remove
		$removeIds = array_map(function ($item) {
			list($gameroom, $ready) = explode('-', $item);
			return $gameroom;
		}, $toRemove);
		foreach ($this->game->getActivityGamerooms()
				->where('activity_id = ?', $activityId)
				->where('gameroom_id IN ?', $removeIds)
				->fetchAll() as $gameroom) {
			$gameroom->delete();
		}
		
		//add
		foreach ($toAdd as $gr) {
			list($gameroom, $ready) = explode('-', $gr);
			$this->game->getActivityGamerooms()->insert(array(
				'activity_id' => $activityId,
				'gameroom_id' => $gameroom,
				'ready' => (bool)$ready
			));
		}
	}
}
