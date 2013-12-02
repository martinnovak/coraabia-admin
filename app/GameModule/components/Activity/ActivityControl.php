<?php

namespace App\GameModule;

use Framework,
	Framework\Kapafaa\KapafaaException,
	Nette;


/**
 * @method setActivityId(string)
 */
class ActivityControl extends Framework\Application\UI\BaseControl
{
	/** @var array */
	private $textKeys = array(
		'activity_name',
		'activity_flavor',
		'activity_task',
		'activity_finish',
		'filter_condition'
	);
	
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Framework\Grido\GridoFactory @inject */
	public $gridoFactory;
	
	/** @var \Framework\Kapafaa\KapafaaParser @inject */
	public $kapafaaParser;
	
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
		$grido->setModel(new Framework\Grido\DataSources\SmartDataSource($this->game->getActivities()))
				->setPrimaryKey('activity_id')
				->setDefaultSort(array('fraction' => 'ASC', 'activity_id' => 'ASC'));
		
		$grido->addColumnText('activity_id', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->activity_id) . '">' . $item->activity_id . '</a>';
				});
		
		$grido->addColumn('name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->activity_id) . '" class="' . strtolower($item->fraction) . '">' . trim($self->translator->translate('activity-name.' . $item->activity_id . '.' . $item->version)) . '</a>';
				});
				
		$versions = $this->game->getAllActivitiesVersions();
		$grido->addColumnNumber('version', 'V')
				->setCustomRender(function ($item) use ($self, $versions) {
					return implode(' | ', array(
						'<span class="' . \Coraabia\ServerEnum::DEV . '">' . (isset($versions[$item->activity_id][\Coraabia\ServerEnum::DEV]) ? $versions[$item->activity_id][\Coraabia\ServerEnum::DEV] : '&times;') . '</span>',
						'<span class="' . \Coraabia\ServerEnum::STAGE . '">' . (isset($versions[$item->activity_id][\Coraabia\ServerEnum::STAGE]) ? $versions[$item->activity_id][\Coraabia\ServerEnum::STAGE] : '&times;') . '</span>',
						'<span class="' . \Coraabia\ServerEnum::BETA . '">' . (isset($versions[$item->activity_id][\Coraabia\ServerEnum::BETA]) ? $versions[$item->activity_id][\Coraabia\ServerEnum::BETA] : '&times;') . '</span>'
					));
				});
				
		$grido->addColumn('fraction', 'Frakce')
				->setSortable()
				->setCustomRender(function ($item) use ($baseUri) {
					return \Nette\Utils\Html::el('img')->src("$baseUri/images/abilities/" .
							($item->fraction ? strtolower($item->fraction) : 'card') .
							".png");
				});
		
		$bots = $this->game->getBotsAsSelect();
		$grido->addColumn('bot_id', 'Bot')
				->setSortable()
				->setCustomRender(function ($item) use ($bots) {
					return $item->bot_id ? ('<i class="icon-user"></i>&nbsp;' . $bots[$item->bot_id]) : '';
				});
		
		$grido->addAction('remove', 'Smazat')
				->setIcon('remove')
				->setCustomHref(function ($item) use ($removeLink) {
					return $removeLink->setParameter('id', $item->activity_id);
				})
				->setConfirm(function ($item) use ($self) {
					return "Opravdu chcete smazat aktivitu '" . $self->translator->translate('activity-name.' . $item->activity_id . '.' . $item->version) . "'?";
				});
		
		return $grido;
	}
	
	
	public function handleDeleteActivity()
	{
		try {
			$this->game->deleteActivity($this->getParameter('id'));
			$this->getPresenter()->flashMessage('Aktivita byla smazána.', 'success');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		$this->redirect('this');
	}
	
	
	public function renderEditActivity()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/activityForm.latte');
		$this->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/activityScripts.latte');
			
			$tmpl->kapafaaDefinitions = $self->kapafaaParser->classes;
			$tmpl->parserLink = $self->link('parseScript');
			
			$hook->addTemplate($tmpl);
		});
		
		$template->render();
	}
	
	
	public function renderCreateActivity()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/activityForm.latte');
		$this->hookManager->listen('scripts', function (\Framework\Hooks\TemplateHook $hook) use ($self) {
			$tmpl = $self->createTemplate();
			$tmpl->setFile(__DIR__ . '/activityScripts.latte');
			
			$tmpl->kapafaaDefinitions = $self->kapafaaParser->classes;
			$tmpl->parserLink = $self->link('parseScript');
			
			$hook->addTemplate($tmpl);
		});
		
		$template->render();
	}
	
	
	/**
	 * @param string $name
	 * @return \Nette\ComponentModel\IComponent
	 */
	public function createComponentActivityForm($name)
	{
		$form = $this->formFactory->create($this, $name);

		$form->addGroup('Nastavení');
			
		$form->addText('activity_id', 'ID')
				->setAttribute('data-bind', "value: activityId, valueUpdate: 'afterkeydown'")
				->setAttribute('class', 'activityId')
				->setRequired('Vyplňte ID aktivity.')
				->addRule(Nette\Forms\Form::MAX_LENGTH, 'Maximální délka ID je %value znaků.', 20)
				->addRule(Nette\Forms\Form::PATTERN, 'ID nesmí obsahovat pomlčku', '[^-]+');
		
		$form->addText('version', 'Verze')
				->setDisabled()
				->setOmitted();
		
		$form->addSelect('rarity', 'Rarita', $this->game->getRarities())
				->setRequired();
		
		$form->addSelect('fraction', 'Frakce', $this->game->getFractions());
		
		$form->addSelect('variant_type', 'Varianta', $this->game->getActivityVariantTypes())
				->setRequired();
		
		$form->addSelect('activity_type', 'Typ aktivity', $this->game->getActivityActivityTypes())
				->setRequired();
		
		$form->addSelect('start_type', 'Typ startu', $this->game->getActivityStartTypes())
				->setRequired();
		
		$form->addSelect('bot_id', 'Bot', array('' => '') + $this->game->getBotsAsSelect());
		
		$gamerooms = array();
		foreach ($this->game->getGamerooms() as $gameroom) {
			$gamerooms[$gameroom->gameroom_id] = $this->translator->translate('gameroom-name.' . $gameroom->gameroom_id . '.' . $gameroom->version);
		}
		$form->addSelect('gameroom_id', 'Gameroom', $gamerooms)
				->setRequired();
		
		$parents = array('' => '');
		foreach ($this->game->getActivities() as $parent) {
			$parents[$parent->activity_id] = $this->translator->translate('activity-name.' . $parent->activity_id . '.' . $parent->version);
		}
		$form->addSelect('parent_id', 'Rodičovská aktivita', $parents);
		
		$form->addGroup('Pozice');
		
		$form->addText('tree', 'Strom')
				->setRequired('Vyplňte strom.')
				->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
				->setOption('description', 'Strom 0 jsou staré aktivity, stromy 1-6 jsou frakční a obecný.');
		
		$form->addText('posx', 'Pozice X')
				->setRequired('Vyplňte x-ovou pozici ve stromě.')
				->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
				->setOption('description', '0 je hlavní linka stromu.');
		
		$form->addText('posy', 'Pozice Y')
				->setRequired('Vyplňte y-ovou pozici ve stromě.')
				->addRule(Nette\Forms\Form::INTEGER, 'Hodnota musí být celé číslo.')
				->setOption('description', '0 je kořen stromu.');
		
		$form->addGroup('Odměna');
		
		$form->addSelect('reward_type', 'Typ odměny', array('' => '') + $this->game->getActivityRewardTypes());
		
		$form->addText('reward_value', 'Hodnota');
		
		$form->addGroup('Obrázky');
		
		$form->addText('mentor', 'Mentor');
		
		$form->addText('art_id', 'Art (@todo)');
		
		foreach ($this->locales->langs as $lang) {
			
			$form->addGroup(strtoupper($lang));
			
			$form->addText('activity_name_' . $lang, 'Jméno')/*
					->setRequired('Vyplňte jméno ' . strtoupper($lang) . ' aktivity')*/;
			
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
			->setAttribute('data-bind', 'value: gameroomList()')
			->setAttribute('class', 'gameroomList');
		$form->addHidden('parentList')
			->setAttribute('data-bind', 'value: parentList()')
			->setAttribute('class', 'parentList');
		
		$form->addTextArea('effect_data', '')
				->setAttribute('class', 'max-width observerScripts')
				->setAttribute('rows', 15)
				//->setAttribute('readonly', 'readonly')
				->setAttribute('data-bind', 'value: toKapafaa');
		
		$form->addText('variable_id', 'Lokální proměnná')
				->setRequired()
				->setAttribute('readonly', 'readonly')
				->setAttribute('class', 'local_var');
		$form->addText('global_var', 'Globální proměnná');
		$form->addText('time_start', 'Začátek')
				->setRequired()
				->addRule(Nette\Forms\Form::PATTERN, 'Čas ve formátu YYYY-MM-DD HH:MM:SS.', '\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}')
				->setAttribute('placeholder', 'YYYY-MM-DD HH:MM:SS');
		$form->addText('time_end', 'Konec')
				->addRule(Nette\Forms\Form::PATTERN, 'Čas ve formátu YYYY-MM-DD HH:MM:SS.', '|\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}')
				->setAttribute('placeholder', 'YYYY-MM-DD HH:MM:SS');
		$form->addText('level_min', 'Min. level')
				->setRequired()
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addText('level_max', 'Max. level')
				->addRule(Nette\Forms\Form::PATTERN, 'Zadejte celé kladné číslo.', '|0|[1-9]\d*');
		$form->addText('influence_min', 'Min. vliv')
				->setRequired()
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addText('influence_max', 'Max. vliv')
				->addRule(Nette\Forms\Form::PATTERN, 'Zadejte celé kladné číslo.', '|0|[1-9]\d*');
		$form->addTextArea('filter_script', '')
				->setAttribute('class', 'max-width filterScript')
				->setAttribute('rows', 15)
				//->setAttribute('readonly', 'readonly')
				->setAttribute('data-bind', 'value: filterToKapafaa');
		
		
		$form->addSubmit('submit', 'Uložit');

		if ($this->activityId === NULL) {
			$form->setDefaults(array(
				'time_start' => $this->locales->timestamp,
				'level_min' => 1,
				'influence_min' => 15
			));
			$form->onSuccess[] = $this->activityCreateFormSuccess;
		} else {
			//activity
			$activity = $this->game->getActivityById($this->activityId);
			$form->setDefaults($activity);
			//texts
			$texts = array();
			foreach ($this->locales->getLangs() as $lang) {
				foreach ($this->textKeys as $key) {
					$texts[$key . '_' . $lang] = $this->translator->getTranslation(str_replace('_', '-', $key) . '.' . $this->activityId . '.' . $activity->version, $lang);
				}
			}
			$form->setDefaults($texts);
			//filter playable
			$form->setDefaults($this->game->getFilterByVersionId($activity->filter_version_playable_id));
			//observer
			$form->setDefaults($this->game->getObserverByVersionId($activity->observer_version_id));
			//gameroom
			$form->setDefaults(array('gameroom_id' => $this->game->getGameroomByVersionId($activity->gameroom_version_id)->gameroom_id));
			//parent activity
			$parent = $this->game->getActivityByVersionId($activity->parent_version_id);
			if ($parent) {
				$form->setDefaults(array('parent_id' => $parent->activity_id));
			}
			
			$form->onSuccess[] = $this->activityEditFormSuccess;
		}
		
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
			$this->kapafaaParser->parse($values->filter_script); //sanity check
			$this->kapafaaParser->parse($values->effect_data); //sanity check
			
			$row = $this->game->createActivity($values);
			
			$this->getPresenter()->flashMessage('Aktivita byla uložena', 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
			$row = NULL;
		}
		
		if ($this->translator instanceof Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
		
		if ($row) {
			$this->getPresenter()->redirect('Activity:editActivity', array('id' => $values->activity_id));
		}
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function activityEditFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
		$row = NULL;
		try {
			$this->kapafaaParser->parse($values->filter_script); //sanity check
			$this->kapafaaParser->parse($values->effect_data); //sanity check
			
			$row = $this->game->updateActivity($values);
			
			$this->getPresenter()->flashMessage('Aktivita byla uložena', 'success');
		} catch (\Exception $e) {
			$form->addError($e->getMessage());
			$row = NULL;
		}
		
		if ($this->translator instanceof Framework\Localization\ICachingTranslator) {
			$this->translator->clean();
		}
		
		$this->redirect('this');
	}
	
	
	/*
	protected function createActivityTexts($activityId, array $values)
	{
		//@todo optimize
		foreach ($values as $key => $value) {
			if (preg_match('/^(' . implode('|', $this->textKeys) . ')_(' . implode('|', $this->locales->langs) . ')$/', $key, $matches)) {
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
		//@todo optimize
		foreach ($toAdd as $gr) {
			list($gameroom, $ready) = explode('-', $gr);
			$this->game->getActivityGamerooms()->insert(array(
				'activity_id' => $activityId,
				'gameroom_id' => $gameroom,
				'ready' => (bool)$ready
			));
		}
	}
	
	
	protected function createPlayableVar($values)
	{
		$variableId = substr($values->activity_id . '_PL', -20);
		$result = $this->game->getSource()->query("INSERT INTO variable", array(
			'variable_id' => $variableId,
			'default_val' => '0',
			'description' => $values->activity_id . ' PLAYABLE',
			'ready' => FALSE
		));
		return $variableId;
	}
	
	
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
		
		$this->game->getActivityPlayableFilters()->insert(array(
			'activity_id' => $values->activity_id,
			'filter_id' => $filter->filter_id,
			'ready' => FALSE
		));
	}


	protected function createAndConnectObserver($values)
	{
		$observer = $this->game->createObserver(array(
			'description' => $values->activity_id,
			'effect_data' => $values->effect_data,
			'effect_desc' => ''
		));
		
		$this->game->getActivityObservers()->insert(array(
			'activity_id' => $values->activity_id,
			'observer_id' => $observer->observer_id,
			'ready' => FALSE
		));
	}
	
	
	protected function updateParentActivities($activityId, array $new, array $original)
	{
		$toRemove = array_diff($original, $new);
		$toAdd = array_diff($new, $original);
		
		$obj = Framework\Kapafaa\ObjectFactory::getActivityPlayableSetter($activityId);
		
		//remove old
		foreach ($this->game->getObservers()
				->select('observer.*, :activity_observer.activity_id')
				->where(':activity_observer.activity_id IN ?', $toRemove)
				->fetchAll() as $observer) {
			$removed = FALSE;
			$scripts = $this->kapafaaParser->parse($observer->effect_data);
			$fin = Framework\Kapafaa\ObjectFactory::getActivityFinishedSetter($observer->activity_id);
			foreach ($scripts as $script) {
				if ($this->kapafaaParser->find($script, $fin)) {
					$removed = TRUE;
					//@todo better. much better
					foreach ($script->objects as $id => $object) {
						if (trim((string)$object) == trim((string)$obj)) {
							$script->removeObject($id);
						}
					}
				}
			}
			if ($removed) {
				$this->game->getObservers()
						->where('observer_id = ?', $observer->observer_id)
						->update(array('effect_data' => implode("\n", $scripts)));
			}
		}
		
		//add new
		foreach ($this->game->getObservers()
				->select('observer.*, :activity_observer.activity_id')
				->where(':activity_observer.activity_id IN ?', $toAdd)
				->fetchAll() as $observer) {
			$added = FALSE;
			$scripts = $this->kapafaaParser->parse($observer->effect_data);
			$fin = Framework\Kapafaa\ObjectFactory::getActivityFinishedSetter($observer->activity_id);
			foreach ($scripts as $script) {
				if ($this->kapafaaParser->find($script, $fin)) {
					$added = TRUE;
					$script->addObject($obj);
				}
			}
			if ($added) {
				$this->game->getObservers()
						->where('observer_id = ?', $observer->observer_id)
						->update(array('effect_data' => implode("\n", $scripts)));
			} else {
				throw new KapafaaException("Nepodařilo se uložit rodičovskou aktivitu '" . $this->translator->translate('activity-name.' . $observer->activity_id) . "', protože nenastavuje splňující proměnnou '" . substr($observer->activity_id . '_FI', -20) . "'.");
			}
		}
	}*/
	
	
	/**
	 * @param string $observerScript
	 * @param string $filterScript
	 */
	public function handleParseScript($observerScript, $filterScript)
	{
		$observer = array_map(function ($item) {
			return array_map(function ($d) {
				return $d->toJson();
			}, $item->objects);
		}, $this->kapafaaParser->parse($observerScript));
		
		$filter = array_map(function ($item) {
			return array_map(function ($d) {
				return $d->toJson();
			}, $item->objects);
		}, $this->kapafaaParser->parse($filterScript));
		
		$this->getPresenter()->sendJson(array('observer' => $observer, 'filter' => $filter));
	}
}
