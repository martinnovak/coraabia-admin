<?php

namespace App;

use Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;


/**
 * Router factory.
 */
class RouterFactory
{
	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('index.php', array('presenter' => 'Sign', 'action' => 'in', 'lang' => 'cs'), Route::ONE_WAY);
		$router[] = new Route('<lang=cs cs|en>', 'Sign:in', Route::ONE_WAY);
		$router[] = new Route('<lang cs|en>/sign/<action out|in>', 'Sign:in');
		$router[] = new Route('<module=game game>/<lang cs|en>/<presenter>/<action>[/<id>]', 'User:profile');
		$router[] = new Route('<module=coraabia coraabia>/<lang cs|en>/<server=dev dev|stage|beta>/<presenter>/<action>[/<id>]', 'User:profile');
		return $router;
	}
}
