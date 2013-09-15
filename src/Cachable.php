<?php

use Doctrine\Common\Annotations\Annotation;

namespace Cached;

/**
 * Use this annotation on methods to cache their return values.
 * Most of the attributes map to their dependency counterparts 
 * of Nette\Caching\Cache, you can use these to configure caching of the method.
 * 
 * @Annotation
 * @Target({"METHOD"})
 */
class Cached {

	/** @var string the cache namespace to store the return value to */
	public $namespace;

	/** @var string the key under which to store the return value */
	public $key;

	/** @var string the caching profile to use */
	public $profile;

	/**
	 * @see \Nette\Caching\Cache::TAGS 
	 * @var array<string>
	 */
	public $tags;

	/** 
	 * @see \Nette\Caching\Cache::EXPIRE
	 * @var string
	 */
	public $expire;

	/**
	 * @see \Nette\Caching\Cache::FILES 
	 * @var array<string>
	 */
	public $files;

	/**
	 * @see \Nette\Caching\Cache::PRIORITY 
	 * @var int
	 */
	public $priority;

	/**
	 * @see \Nette\Caching\Cache::SLIDING
	 * @var bool
	 */
	public $sliding;

}
