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
		
		$grido->addColumnText('activity_id', 'ID')
				->setSortable()
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->activity_id) . '" class="' . strtolower($item->fraction) . '">' . $item->activity_id . '</a>';
				});
		
		$grido->addColumn('translated_name', 'Jméno')
				->setCustomRender(function ($item) use ($self, $editLink) {
					return '<a href="' . $editLink->setParameter('id', $item->activity_id) . '" class="' . strtolower($item->fraction) . '">' . trim($self->translator->translate('activity-name.' . $item->activity_id)) . '</a>';
				});
				
		$grido->addColumn('fraction', 'Frakce')
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
	
	
	public function renderEditActivity()
	{
		$self = $this;
		$template = $this->template;
		$template->setFile(__DIR__ . '/activityForm.latte');
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
		$self = $this;
		$form = $this->formFactory->create($this, $name);

		$form->addGroup('Nastavení');
			
		$form->addText('activity_id', 'ID')
				->setAttribute('data-bind', "value: activityId, valueUpdate: 'afterkeydown'")
				->setAttribute('class', 'activityId')
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
			->setAttribute('data-bind', 'value: gameroomList()')
			->setAttribute('class', 'gameroomList');
		$form->addHidden('parentList')
			->setAttribute('data-bind', 'value: parentList()')
			->setAttribute('class', 'parentList');
		
		$form->addTextArea('observer_scripts', '')
				->setAttribute('class', 'max-width observerScripts')
				->setAttribute('rows', 15)
				->setAttribute('readonly', 'readonly')
				->setAttribute('data-bind', 'value: toKapafaa');
		
		$form->addText('local_var', 'Lokální proměnná')
				->setRequired()
				->setAttribute('readonly', 'readonly')
				->setAttribute('class', 'local_var');
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
				->addRule(Nette\Forms\Form::PATTERN, 'Zadejte celé kladné číslo.', '|0|[1-9]\d*');
		$form->addText('influence_min', 'Min. vliv')
				->setRequired()
				->addRule(Nette\Forms\Form::INTEGER);
		$form->addText('influence_max', 'Max. vliv')
				->addRule(Nette\Forms\Form::PATTERN, 'Zadejte celé kladné číslo.', '|0|[1-9]\d*');
		$form->addTextArea('filter_scripts', '')
				->setAttribute('class', 'max-width filterScripts')
				->setAttribute('rows', 15)
				->setAttribute('readonly', 'readonly')
				->setAttribute('data-bind', 'value: filterToKapafaa');
		
		
		$form->addSubmit('submit', 'Uložit');

		if ($this->activityId === NULL) {
			$form->setDefaults(array(
				'activity_id' => '_C',
				'time_start' => $this->locales->timestamp,
				'level_min' => 1,
				'influence_min' => 15
			));
			$form->onSuccess[] = $this->activityCreateFormSuccess;
		} else {
			//activity
			$activity = $this->game->getActivities()
					->where('activity_id = ?', $this->activityId)
					->fetch();
			//texts
			$texts = array();
			foreach ($this->locales->getLangs() as $lang) {
				foreach ($this->textKeys as $key) {
					$texts[$key . '_' . $lang] = $this->translator->getTranslation(str_replace('_', '-', $key) . '.' . $this->activityId, $lang);
				}
			}
			//filter
			$filter = $this->game->getFilters()
					->where(':activity_filter_playable.activity_id = ?', $this->activityId)
					->fetch()
					->toArray();
			$filter['local_var'] = $filter['variable_id'];
			$filter['filter_scripts'] = $filter['script'];
			//observer
			$observer = array('observer_scripts' => $this->game->getObservers()
					->where(':activity_observer.activity_id = ?', $this->activityId)
					->fetch()
					->effect_data);
			//gamerooms
			$gamerooms = array('gameroomList' => implode('--', array_map(function ($gr) {
				return $gr->gameroom_id . '-' . ($gr->ag_ready ? '1' : '0');
			}, $this->game->getGamerooms()
					->select('gameroom.*, :activity_gameroom.ready AS ag_ready')
					->where(':activity_gameroom.activity_id = ?', $this->activityId)
					->fetchAll())));
			//parent activities
			$parents = array('parentList' => implode('--', array_map(function ($item) {
				return $item->activity_id;
			}, $this->game->getParentActivities($this->activityId)
					->fetchAll())));
			
			$form->setDefaults(array_merge(
				$activity->toArray(),
				$texts,
				$filter,
				$observer,
				$gamerooms,
				$parents
			));
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
			$this->game->getSource()->beginTransaction();
			
			//activity, texts, gamerooms
			$row = $this->game->createActivity((array)$values);
			$this->createActivityTexts($values->activity_id, (array)$values);
			$this->updateActivityGamerooms($values->activity_id, array_filter(explode('--', $values->gameroomList)), array());
			
			//filter
			$values->local_var = $this->createPlayableVar($values);
			$this->kapafaaParser->parse($values->filter_scripts); //sanity check
			$this->createAndConnectFilter($values);
			
			//observer
			$observerScripts = $this->kapafaaParser->parse($values->observer_scripts); //sanity check
			$finished = FALSE;
			$obj = Framework\Kapafaa\ObjectFactory::getActivityFinishedSetter($values->activity_id);
			foreach ($observerScripts as $script) {
				if ($this->kapafaaParser->find($script, $obj)) {
					$finished = TRUE;
				}
			}
			if (!$finished) {
				throw new KapafaaException("Observer nenastavuje splňující proměnnou '{$values->activity_id}_FI'.");
			}
			$this->createAndConnectObserver($values);
			
			//parent activities
			$this->updateParentActivities($values->activity_id, array_filter(explode('--', $values->parentList)), array());
			
			$this->game->getSource()->commit();
		} catch (\Exception $e) {
			$this->game->getSource()->rollBack();
			$form->addError($e->getMessage());
			$row = NULL;
		}
		
		if ($row) {
			$this->getPresenter()->redirect('Activity:editActivity', array('id' => $values['activity_id']));
		}
	}
	
	
	/**
	 * @param \Nette\Application\UI\Form $form
	 */
	public function activityEditFormSuccess(Nette\Application\UI\Form $form)
	{
		$values = $form->getValues();
	}
	

	/**
	 * @param string $activityId
	 * @param array $values
	 */
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
	
	
	/**
	 * @param \Nette\ArrayHash|array $values
	 */
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
		
		$this->game->getActivityPlayableFilters()->insert(array(
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
		
		$this->game->getActivityObservers()->insert(array(
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
	}
	
	
	/**
	 * @todo
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
