<?php

namespace App;

use Framework,
	Coraabia;


class UserControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;

	
	public function handleChangeLang()
	{
		try {
			$lang = $this->getParameter('lang');
			$user = $this->game->getUserdata()->where('user_id = ?', $this->getPresenter()->getUser()->getId())->fetch();
			$user->update(array('lang' => $lang));
			$this->getPresenter()->getUser()->getIdentity()->lang = $lang;
			$this->getPresenter()->flashMessage("Jazyk byl změněn na '" . strtoupper($lang) . "'.", 'info');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->getPresenter()->redirect('this');
	}
	
	
	public function handleChangeServer()
	{
		try {
			$server = $this->getParameter('server');
			$user = $this->game->getUserdata()->where('user_id = ?', $this->getPresenter()->getUser()->getId())->fetch();
			$user->update(array('server' => $server));
			$this->getPresenter()->getUser()->getIdentity()->server = $server;
			$this->getPresenter()->flashMessage("Nyní jste na serveru '" . strtoupper($server) . "'.", 'info');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->getPresenter()->redirect('this', array('server' => $server));
	}
	
	
	public function renderProfile()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/profile.latte');
		$template->user = $this->getPresenter()->getUser();
		$template->render();
	}
	
	
	public function handleChangeModule()
	{
		$currentUser = $this->getPresenter()->getUser();
		try {
			$module = $this->getParameter('mdl');
			$user = $this->game->getUserdata()->where('user_id = ?', $currentUser->getId())->fetch();
			$user->update(array('module' => $module));
			$currentUser->getIdentity()->module = $module;
			$this->getPresenter()->flashMessage("Nyní jste v modulu '" . strtoupper($module) . "'.", 'info');
		} catch (\Exception $e) {
			$this->getPresenter()->flashMessage($e->getMessage(), 'error');
		}
		
		$this->getPresenter()->redirect(
			':' . ($currentUser->getIdentity()->module == Coraabia\ModuleEnum::CORAABIA ? 'Coraabia' : 'Game') . ':User:profile',
			array(
				'server' => $currentUser->getIdentity()->module == Coraabia\ModuleEnum::CORAABIA ? $currentUser->getIdentity()->server : ''
			)
		);
	}
}
