<?php

namespace Model;

use Nette;



/**
 * Users management.
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var \Model\Game */
	private $game;
	
	/** @var string */
	private $applicationSecret;
	
	/** @var string */
	private $hashAlgorithm;



	/**
	 * @param \Model\Game $game
	 * @param type $applicationSecret
	 * @param type $hashAlgorithm 
	 */
	public function __construct(Game $game, $applicationSecret, $hashAlgorithm)
	{
		$this->game = $game;
		$this->applicationSecret = $applicationSecret;
		$this->hashAlgorithm = $hashAlgorithm;
	}



	/**
	 * Performs an authentication.
	 * @return \Nette\Security\Identity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$row = $this->game->userdata->where('username = ?', $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Přihlášení se nezdařilo.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->calculateHash($password, $this->applicationSecret, $this->hashAlgorithm)) {
			throw new Nette\Security\AuthenticationException('Přihlášení se nezdařilo.', self::INVALID_CREDENTIAL);
		}

		$arr = $row->toArray();
		unset($arr['password']);
		return new Nette\Security\Identity($row->user_id, $row->role_id, $arr);
	}



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password, $salt, $algorithm)
	{
		return hash($algorithm, $salt . hash($algorithm, $password) . $salt);
	}
}
