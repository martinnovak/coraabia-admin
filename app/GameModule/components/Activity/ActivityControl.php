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
			
			$tmpl->kapafaaDefinitions = $self->kapafaaParser->classes;
			
			$hook->addTemplate($tmpl);
		});
		
		$this->updateParentActivities('LALALA', array('TEST_I'), array());
		
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
				//->setAttribute('data-bind', 'value: activityId')
				->setRequired('Vyplňte ID aktivity.')
				->addRule(Nette\Forms\Form::MAX_LENGTH, 'Maximální délka ID je %value znaků.', 20)
				->addRule(Nette\Forms\Form::PATTERN, 'ID musí končit jednou z přípon _C, _U, _R, _G a nesmí obsahovat pomlčku', '[^-]+_[CURG]');
		
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
		
		$bots = array('' => '');
		foreach ($this->game->getBots()->fetchAll() as $bot) {
			$bots[$bot->bot_id] = $bot->name;
		}
		$form->addSelect('bot_id', 'Bot', $bots);
		
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
		
		$form->addGroup('Odměna');
		
		$rewardTypes = $this->game->getActivityRewardTypes();
		$rewardTypes = array('' => '') + array_combine($rewardTypes, $rewardTypes);
		$form->addSelect('reward_type', 'Typ odměny', $rewardTypes);
		
		$form->addText('reward_value', 'Hodnota');
		
		$form->addGroup('Obrázky');
		
		$form->addText('authority', 'Zadavatel');
		
		$form->addText('art_id', 'Art (todo)');
		
		foreach ($this->locales->langs as $lang) {
			
			$form->addGroup(strtoupper($lang));
			
			$form->addText('activity_name_' . $lang, 'Jméno')
					->setRequired('Vyplňte jméno ' . strtoupper($lang) . ' aktivity');
			
			$form->addTextArea('activity_flavor_' . $lang, 'Flavor')
					->setAttribute('rows', 10);
			
			$form->addTextArea('activity_finish_' . $lang, 'Text splnění')
					->setAttribute('rows', 10);
			
			$form->addTextArea('activity_task_' . $lang, 'Text zadání')
					->setAttribute('rows', 2);
			
			$form->addTextArea('filter_condition_' . $lang, 'Text filtru')
					->setAttribute('rows', 2);
		}

		$form->setCurrentGroup();
		
		$form->addHidden('gameroomList')
			->setAttribute('data-bind', 'value: gameroomList()');
		$form->addHidden('parentList')
			->setAttribute('data-bind', 'value: parentList()');
		
		$form->addText('local_var', 'Lokální proměnná')
				->setRequired()
				->setAttribute('readonly', 'readonly')/*
				->setAttribute('data-bind', 'value: playableVarId')*/;
		$form->addText('global_var', 'Globální proměnná');
		$form->addText('time_start', 'Začátek')
				->setRequired()
				->addRule(Nette\Forms\Form::PATTERN, 'Čas ve formátu YYYY-MM-DD HH:MM:SS.', '\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}');
		$form->addText('time_end', 'Konec')
				->addRule(Nette\Forms\Form::PATTERN, 'Čas ve formátu YYYY-MM-DD HH:MM:SS.', '|\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}');
		$form->addText('level_min', 'Min. level')
				->setRequired()
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addText('level_max', 'Max. level')
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addText('influence_min', 'Min. vliv')
				->setRequired()
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addText('influence_max', 'Max. vliv')
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addTextArea('filter_scripts', '')
				->setAttribute('class', 'max-width')
				->setAttribute('rows', 15)
				->setAttribute('readonly', 'readonly')
				->setAttribute('data-bind', 'value: filterToKapafaa');
		
		
		$form->addSubmit('submit', 'Uložit');
		
		$form->setDefaults(array(
			'activity_id' => '_C',
			'local_var' => '_C_PL',
			'time_start' => $this->locales->timestamp,
			'level_min' => 1,
			'influence_min' => 15,
			'filter_scripts' => "(\n)\n"
		));
		
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
			
			//activity, texts, gamerooms
			$row = $this->game->createActivity((array)$values);
			$this->createActivityTexts($values->activity_id, (array)$values);
			$this->updateActivityGamerooms($values->activity_id, explode('--', $values->gameroomList), array());
			
			//filter
			$values->local_var = $this->createPlayableVar($values)->variable_id;
			$this->kapafaaParser->parse($values->filter_scripts); //sanity check
			$this->createAndConnectFilter($values);
			
			//observer
			$observerScripts = $this->kapafaaParser->parse($values->observer_scripts); //sanity check
			$finished = FALSE;
			foreach ($observerScripts as $script) {
				if ($this->kapafaaParser->find($script, $this->getFinishedKapafaaObject($values->activity_id))) {
					$finished = TRUE;
				}
			}
			if (!$finished) {
				throw new Framework\Kapafaa\KapafaaException("Observer nenastavuje splňující proměnnou {$values->activity_id}_FI.");
			}
			$this->createAndConnectObserver($values);
			
			//parent activities
			//@todo
			
			$this->game->getSource()->commit();
		} catch (\Exception $e) {
			$this->game->getSource()->rollBack();
			$form->addError($e->getMessage());
		}
		
		if ($row) {
			$this->getPresenter()->redirect('Activity:editActivity', array('id' => $values['activity_id']));
		}
	}
	

	/**
	 * @param string $activityId
	 * @param array $values
	 */
	protected function createActivityTexts($activityId, array $values)
	{
		$keys = array(
			'activity_name',
			'activity_flavor',
			'activity_task',
			'activity_finish',
			'filter_condition'
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
		if ($this->translator instanceof \Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
	}
	
	
	/**
	 * @param string $activityId
	 * @param array $new
	 * @param array $original
	 */
	protected function updateActivityGamerooms($activityId, array $new, array $original)
	{
		$toRemove = array_diff($original, $new);
		$toAdd = array_diff($new, $original);
		
		//remove
		$removeIds = array_map(function ($item) {
			list($gameroom, ) = explode('-', $item);
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
	
	
	/**
	 * @param \Nette\ArrayHash|array $values
	 */
	protected function createPlayableVar($values)
	{
		return $this->game->getSource()->query("INSERT INTO variable", array(
			'variable_id' => substr($values->activity_id . '_PL', -20),
			'default_val' => '0',
			'description' => $values->activity_id . ' PLAYABLE',
			'ready' => FALSE
		));
	}
	
	
	/**
	 * @param \Nette\ArrayHash|array $values
	 */
	protected function createAndConnectFilter($values)
	{
		$filter = $this->game->createFilter(array(
			'variable_id' => $values->local_var,
			'global_var' => $values->global_var ?: NULL,
			'time_start' => $values->time_start,
			'time_end' => $values->time_end ?: NULL,
			'level_min' => $values->level_min,
			'level_max' => $values->level_max ?: NULL,
			'influence_min' => $values->influence_min,
			'influence_max' => $values->influence_max ?: NULL,
			'script' => $values->filter_scripts,
			'ready' => FALSE
		));
		
		$this->game->getSource()->query("INSERT INTO activity_filter_playable", array(
			'activity_id' => $values->activity_id,
			'filter_id' => $filter->filter_id,
			'ready' => FALSE
		));
	}


	/**
	 * @param \Nette\ArrayHash|array $values
	 */
	protected function createAndConnectObserver($values)
	{
		$observer = $this->game->createObserver(array(
			'description' => $values->activity_id,
			'effect_data' => $values->observer_scripts,
			'effect_desc' => ''
		));
		
		$this->game->getSource()->query("INSERT INTO activity_observer", array(
			'activity_id' => $values->activity_id,
			'observer_id' => $observer->observer_id,
			'ready' => FALSE
		));
	}
	
	
	/**
	 * @param string $activityId
	 * @param array $new
	 * @param array $original
	 */
	protected function updateParentActivities($activityId, array $new, array $original)
	{
		$toRemove = array_diff($original, $new);
		$toAdd = array_diff($new, $original);
		
		//remove old
		//@todo
		
		//add new
		//@todo
		//přidat do skriptů nastavujících finished proměnnou daných aktivit v observrech daných aktivit řádek nastavující mojí playable proměnnou na 1
		//
		//SELECT observer.* FROM observer LEFT JOIN activity_observer WHERE activity_observer.activity_id IN ($toAdd)
		//naparsovat jejich skripty, najít v nich finished proměnné = 1, přidat playable proměnnou = 1, uložit
		//eff.world.gen.local(@xxx_PL@ = 1)
		
		$obj = new Framework\Kapafaa\Effects\GenericLocal(
				substr($activityId . '_PL', -20)
				, new Framework\Kapafaa\Modifications\Number(
						new Framework\Kapafaa\Operators\Equals(),
						1)
				);
		
		foreach ($this->game->getObservers()
				->select('observer.*, :activity_observer.activity_id')
				->where(':activity_observer.activity_id', $toAdd)
				->fetchAll() as $observer) {
			foreach ($this->kapafaaParser->parse($observer->effect_data) as $script) {
				if ($this->kapafaaParser->find($script, $this->getFinishedKapafaaObject(substr($observer->activity_id, -20)))) {
					$script->addObject($obj);
					//@todo ulozit, no break
				}
			}
		}
	}
	
	
	/**
	 * @param string $activityId
	 */
	protected function getFinishedKapafaaObject($activityId)
	{
		//eff.world.gen.local(@VARIABLE@ = 1)
		return new Framework\Kapafaa\Effects\GenericLocal(
				substr($activityId . '_FI', -20),
				new Framework\Kapafaa\Modifications\Number(
						new Framework\Kapafaa\Operators\Equals(),
						1)
				);
	}
}
