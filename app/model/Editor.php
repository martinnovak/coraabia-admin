<?php

namespace Model;


class Editor extends Model
{
	/**
	 * @param string $username
	 * @return \Nette\Database\IRow|NULL
	 */
	public function getValidUserByName($username)
	{
		foreach ($this->getDatasource()->getUserdata() as $user) {
			if ($user->enabled && $user->username == $username) {
				return $user;
			}
		}
	}
	
	
	/**
	 * @param int $userId
	 * @return \Nette\Database\IRow|NULL
	 */
	public function getUserById($userId)
	{
		foreach ($this->getDatasource()->getUserdata() as $user) {
			if ($user->user_id == $userId) {
				return $user;
			}
		}
	}
	
	
	/**
	 * @param int $userId
	 * @param array $params
	 */
	public function updateUser($userId, array $params)
	{
		$this->getDatasource()->updateUser((int)$userId, $params);
	}
	
	
	/**
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->getDatasource()->getPermissions();
	}
}
