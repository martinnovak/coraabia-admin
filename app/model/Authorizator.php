<?php

namespace Model;

use Nette;


/**
 * Permission management.
 * @method \Nette\Caching\IStorage getStorage()
 */
class Authorizator extends Nette\Security\Permission
{
	/** @var \Model\Editor */
	private $editor;
	
	/** @var \Nette\Caching\IStorage */
	private $storage;
	
	
	/**
	 * @param \Model\Editor $editor
	 * @param \Nette\Caching\IStorage $storage
	 */
	public function __construct(Editor $editor, Nette\Caching\IStorage $storage)
	{
		$this->editor = $editor;
		$this->storage = $storage;
	}
	
	
	/**
	 * Sets permissions
	 */
	public function setupPermissions()
	{
		$cache = new Nette\Caching\Cache($this->storage, str_replace('\\', '.', get_class()));
		if (NULL === ($permissions = $cache->load('permissions'))) {
			$permissions = array();
			foreach ($this->editor->getPermissions() as $row) {
				$permissions[] = $row->toArray();
			}
			$cache->save('permissions', $permissions);
		}
		
		foreach ($permissions as $p) {
			//setup roles
			if (!$this->hasRole($p['role_id'])) {
				$this->addRole($p['role_id']);
			}
			
			//setup resources
			$resource = $this->buildResourceName($p['module'], $p['server'], $p['resource']);
			if (!$this->hasResource($resource)) {
				$this->addResource($resource);
			}
			
			//setup permissions
			$this->allow($p['role_id'], $resource);
		}
	}
	
	
	/**
	 * @param \Nette\Security\User $user
	 */
	public function setupOnLoggedOut(Nette\Security\User $user)
	{
		$self = $this;
		$user->onLoggedOut[] = function () use ($self) {
			$cache = new Nette\Caching\Cache($self->storage, str_replace('\\', '.', get_class($self)));
			$cache->remove('permissions');
		};
	}
	
	
	/**
	 * @param string $module
	 * @param string $server
	 * @param string $resource
	 * @return string 
	 */
	public function buildResourceName($module, $server, $resource)
	{
		return strtolower("$module/$server/$resource");
	}
	
	
	/**
	 * Missing roles or resources do not throw exception and act as not allowed instead.
	 * @param string|Permission::ALL|IRole $role
	 * @param string|Permission::ALL|IResource $resource
	 * @param string|Permission::ALL $privilege
	 * @return boolean 
	 */
	public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL)
	{
		try {
			return parent::isAllowed($role, $resource, $privilege);
		} catch (\Exception $e) {
			//dlog($e);
		}
		return FALSE;
	}
}
