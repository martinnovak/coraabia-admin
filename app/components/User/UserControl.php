<?php

namespace App;

use Nette,
	Framework;


class UserControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	
	public function handleChangeLang()
	{
		try {
			$lang = $this->getParameter('lang');
			$user = $this->game->userdata->where('user_id = ?', $this->getPresenter()->getUser()->getId())->fetch();
			$user->update(array('lang' => $lang));
			$this->getPresenter()->getUser()->getIdentity()->lang = $lang;
			$this->presenter->flashMessage("Jazyk byl změněn na '" . strtoupper($lang) . "'.", 'info');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
		
		$this->presenter->redirect('this');
	}
	
	
	public function handleChangeServer()
	{
		try {
			$server = $this->getParameter('server');
			$this->presenter->flashMessage("Nyní jste na serveru '" . strtoupper($server) . "'.", 'info');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
		
		$this->presenter->redirect('this', array('server' => $server));
	}
	
	
	public function renderProfile()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/profile.latte');
		$template->user = $this->getPresenter()->getUser();
		$template->render();
	}
}
