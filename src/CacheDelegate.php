<?php

namespace Cached;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;

/**
 * Class that delegates calls to a <code>\Nette\Caching\Cache</code> instance.
 *
 * @author Radek Ježdík <jezdik.radek@gmail.com>
 */
class CacheDelegate {

	/** @var \Nette\Caching\IStorage\Cache */
	private $cache;

	/** @var mixed the key under which to store the value */
	private $key;

	/** @var \Cached\CacheOptions */
	private $options;

	public function __construct(IStorage $storage, $key, CacheOptions $options) {
		$this->cache = new Cache($storage, $options->namespace ? : 'cached');
		$this->key = $key;
		$this->options = $options;
	}


	/**
	 * Returns true if there is a value stored under the key.
	 * 
	 * @return bool
	 */
	public function hasValue() {
		return isset($this->cache[$this->key]);
	}


	/**
	 * Returns the value stored in the cache.
	 * 
	 * @return mixed
	 */
	public function getValue() {
		return $this->cache[$this->key];
	}


	/**
	 * Saves the given value in the cache.
	 * 
	 * @param mixed the value to save
	 */
	public function save($value) {
		$this->cache->save($this->key, $value, $this->options->dependencies);
	}

}

