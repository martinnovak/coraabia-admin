<?php

namespace Framework\Application\UI;

use Nette,
	Framework;


/**
 * Secured presenter.
 */
abstract class SecuredPresenter extends BasePresenter
{
	
	/**
	 * @param mixed $element
	 */
	public function checkRequirements($element)
	{
		try {
			$check = $this->__checkRequirements($element);
		} catch (Framework\Security\NotLoggedInException $e) {
			$this->redirect(':Sign:out', array('backlink' => $this->storeRequest()));
		} catch (\Exception $e) {
			$check = FALSE;
		}
		
		if (!$check) {
			throw new Nette\Application\ForbiddenRequestException;
		}
	}
	
	
	/**
	 * @param mixed $element
	 * @throws Nette\Application\ForbiddenRequestException 
	 */
	public function __checkRequirements($element)
	{
		if (!$this->getUser()->isLoggedIn()) {
			throw new Framework\Security\NotLoggedInException;
			return FALSE;
		}
		
		//@todo THIS IS UGLY
		if (isset($this->getContext()->parameters['secured']) && !$this->getContext()->parameters['secured']) {
			return TRUE;
		}
		
		if ($this->signal !== NULL) {
			$resource = $this->getUser()->getAuthorizator()
					->buildResourceName($this->locales->module, $this->locales->server, $this->signal[1]);
			if (!$this->getUser()->isAllowed($resource)) {
				return FALSE;
			}
		}
		
		$resource = $this->getUser()->getAuthorizator()
				->buildResourceName($this->locales->module, $this->locales->server, $this->getParameter('action'));
		if (!$this->getUser()->isAllowed($resource)) {
			return FALSE;
		}
		
		return TRUE;
	}
}
