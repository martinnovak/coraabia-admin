<?php

namespace Framework\Application\UI;

use Nette;



/**
 * Secure presenter.
 */
abstract class SecurePresenter extends BasePresenter
{
	
	public function startup()
	{
		parent::startup();
		
		if (!$this->user->isLoggedIn()) {
			$this->redirect('Sign:out', array('backlink' => $this->storeRequest()));
		}
		
		// @TODO test, debug
		if (NULL !== $this->signal) {
			$resource = $this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->signal);
			if (!$this->user->isAllowed($resource)) {
				throw new Nette\Application\BadRequestException;
			}
		}
		
		$resource = $this->user->getAuthorizator()->buildResourceName($this->locales->server, $this->getParameter('action'));
		if (!$this->user->isAllowed($resource)) {
			throw new Nette\Application\BadRequestException;
		}
	}
}
