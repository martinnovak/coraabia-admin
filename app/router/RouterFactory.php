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
	private $defaults = array(
		'lang' => 'cs',
		'server' => 'dev',
		'presenter' => 'User',
		'action' => 'showProfile'
	);
	
	
	
	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('index.php', $this->defaults, Route::ONE_WAY);
		$router[] = new Route('<lang cs|en>/<server dev|stage|beta|static>/<presenter>/<action>[/<id>]', $this->defaults);
		return $router;
	}
}
