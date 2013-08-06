<?php

namespace App;

use Nette,
	Framework;



class UserControl extends Framework\Application\UI\BaseControl
{
	/** @var \Model\Game @inject */
	public $game;
	
	/** @var \Nette\Security\User @inject */
	public $user;
	
	
	
	public function handleChangeLang()
	{
		try {
			$lang = $this->getParameter('lang');
			$user = $this->game->userdata->where('user_id = ?', $this->user->getId())->fetch();
			$user->update(array('lang' => $lang));
			$this->user->storage->setIdentity(new Nette\Security\Identity($user->user_id, $user->role_id, $user->toArray()));
			$this->presenter->flashMessage("Jazyk byl změněn na '" . strtoupper($lang) . "'", 'info');
		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'error');
		}
		
		$this->presenter->redirect('this');
	}
}
