<?php

namespace Model;

use Nette;



/**
 * Localization.
 * @method \Nette\Caching\IStorage getStorage()
 * @method array getTranslations()
 */
class Translator extends Nette\Object implements Nette\Localization\ITranslator
{
	/** @var \Model\Game */
	private $game;
	
	/** @var \Nette\Caching\IStorage */
	private $storage;
	
	/** @var \Model\Locales */
	private $locales;
	
	/** @var array of arrays of strings */
	private $translations;
	
	
	
	/**
	 * @param \Model\Game $game
	 * @param \Nette\Caching\IStorage $storage
	 * @param \Model\Locales $locales 
	 */
	public function __construct(Game $game, Nette\Caching\IStorage $storage, Locales $locales)
	{
		$this->game = $game;
		$this->storage = $storage;
		$this->locales = $locales;
	}
	
	
	
	public function setupTranslations()
	{
		$cache = new Nette\Caching\Cache($this->storage, str_replace('\\', '.', get_class()));
		if (NULL === ($translations = $cache->load('translations'))) {
			$translations = array();
			foreach ($this->game->translations->fetchAll() as $row) {
				$translations[$row->lang][$row->key] = $row->value;
			}
			$cache->save('translations', $translations);
		}
		
		$this->translations = $translations;
	}
	
	
	
	/**
	 * @param \Nette\Security\User $user
	 */
	public function setupOnLoggedOut(Nette\Security\User $user)
	{
		$self = $this;
		$user->onLoggedOut[] = function () use ($self) {
			$cache = new Nette\Caching\Cache($self->storage, str_replace('\\', '.', get_class($self)));
			$cache->remove('translations');
		};
	}
	
	
	
	/**
     * Translates the given string.
     * @param  string   message
     * @param  int      plural count
     * @return string
     */
    public function translate($message, $count = NULL)
    {
		$lang = $this->locales->lang;
		if (isset($this->translations[$lang][$message])) {
			$message = $this->translations[$lang][$message];
		} else {
			//dlog("Missing translation for '$message' in language '$lang'.");
		}
        return $message;
    }
	
	
	
	/**
	 * @param string $key 
	 * @param string|NULL $lang
	 */
	public function getTranslation($key, $lang = NULL)
	{
		if ($lang === NULL) {
			$lang = $this->locales->lang;
		}
		if (isset($this->translations[$lang][$key])) {
			return $this->translations[$lang][$key];
		} else {
			return '';
		}
	}
}
