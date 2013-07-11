<?php

namespace Framework\Application\UI;

use Nette;



/**
 * Secure presenter.
 */
abstract class SecuredPresenter extends BasePresenter
{
	
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
		
		if (!$this->user->isLoggedIn()) {
			$this->redirect('Sign:out', array('backlink' => $this->storeRequest()));
		}
		
		if (NULL !== $this->signal) {
			if (!$this->user->isAllowed($this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->signal[1]))) {
				throw new Nette\Application\ForbiddenRequestException;
			}
		}
		
		if (!$this->user->isAllowed($this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->getParameter('action')))) {
			throw new Nette\Application\ForbiddenRequestException;
		}
	}
}
