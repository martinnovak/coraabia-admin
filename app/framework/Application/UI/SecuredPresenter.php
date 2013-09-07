<?php

namespace Framework\Application\UI;

use Nette;


/**
 * Secure presenter.
 */
abstract class SecuredPresenter extends BasePresenter
{
	/**
	 * @param mixed $element
	 * @throws Nette\Application\ForbiddenRequestException 
	 */
	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
	
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect(':Sign:out', array('backlink' => $this->storeRequest()));
		}
		
		//@todo THIS IS UGLY
		$secured = $this->getContext()->parameters['secured'];
		if (isset($secured) && !$secured) {
			return;
		}
		
		if ($this->signal !== NULL) {
			$resource = $this->getUser()->getAuthorizator()
					->buildResourceName($this->locales->module, $this->locales->server, $this->signal[1]);
			if (!$this->getUser()->isAllowed($resource)) {
				throw new Nette\Application\ForbiddenRequestException("Nemáte práva na zdroj '$resource'.");
			}
		}
		
		$resource = $this->getUser()->getAuthorizator()
				->buildResourceName($this->locales->module, $this->locales->server, $this->getParameter('action'));
		if (!$this->getUser()->isAllowed($resource)) {
			throw new Nette\Application\ForbiddenRequestException("Nemáte práva na zdroj '$resource'.");
		}
	}
}
