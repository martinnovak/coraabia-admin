<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;


/**
 * Router factory.
 */
class RouterFactory
{
	/** @var array */
	public $defaults = array(
		'index' => array(
			'lang' => 'cs',
			'presenter' => 'Sign',
			'action' => 'out'
		),
		'sign' => array(
			'lang' => 'cs',
			'presenter' => 'Sign',
		),
		'modules' => array(
			'lang' => 'cs',
			'module' => 'game',
			'server' => 'dev',
			'presenter' => 'User',
			'action' => 'showProfile'
		)
	);
	
	
	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('index.php', $this->defaults['index'], Route::ONE_WAY);
		$router[] = new Route('<lang cs|en>/sign/<action>', $this->defaults['sign']);
		$router[] = new Route('<lang cs|en>/<module game|coraabia>/<server dev|stage|beta>/<presenter>/<action>[/<id>]', $this->defaults['modules']);
		return $router;
	}
}
