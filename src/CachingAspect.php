<?php

namespace Cached;

use Doctrine\Common\Annotations\AnnotationReader;
use Kdyby\Aop;

/**
 * The caching aspect that does all the hard work - caching.
 *
 * @author Radek Ježdík <jezdik.radek@gmail.com>
 */
class CachingAspect {

	/**
	 * The cache storage to use.
	 * @var \Nette\Caching\IStorage
	 */
	private $storage;

	/**
	 * 
	 * @var ICacheProfile
	 */
	private $cacheProfiles;

	/**
	 * @var AnnotationReader
	 */
	private $reader;


	/**
	 * @param \Nette\Caching\IStorage $storage the cache storage to use
	 * @param \Cached\ICacheProfile $cacheProfiles the cache profiles container
	 */
	function __construct(\Nette\Caching\IStorage $storage, ICacheProfile $cacheProfiles) {
		$this->storage = $storage;
		$this->cacheProfiles = $cacheProfiles;
		$this->reader = new AnnotationReader();
	}


	/**
	 * @Aop\Around("methodAnnotatedWith(Cached\Cached)")
	 */
	public function process(Aop\JoinPoint\AroundMethod $m) {
		$ann = $this->getAnnotation($m);

		$options = $this->cacheProfiles->getOptions($ann);

		if(!$options->enabled) {
			return $m->proceed();
		}

		$key = $ann->key ? : $this->createKey($m);

		$cache = $this->createCache($key, $options);

		if($cache->hasValue()) {
			return $cache->getValue();
		} else {
			$value = $m->proceed();
			$cache->save($value);
			return $value;
		}
	}


	private function createKey(Aop\JoinPoint\AroundMethod $m) {
		$class = $m->getTargetObjectReflection()->getName();
		$method = $m->getTargetReflection()->getName();
		$arguments = serialize($m->getArguments());

		return "$class::$method($arguments)";
	}


	/**
	 * @param \Kdyby\Aop\JoinPoint\AroundMethod $m
	 * @return \Cached\Cached
	 */
	private function getAnnotation(Aop\JoinPoint\AroundMethod $m) {
		return $this->reader->getMethodAnnotation($m->getTargetReflection(), 'Cached\Cached');
	}


	/**
	 * @param string $key
	 * @param \Cached\CacheOptions $options
	 * @return \Cached\CacheDelegate
	 * @internal used for tests
	 */
	public function createCache($key, CacheOptions $options) {
		return new CacheDelegate($this->storage, $key, $options);
	}


	/**
	 * @param \Doctrine\Common\Annotations\AnnotationReader $reader
	 * @internal used for tests
	 */
	public function setReader(AnnotationReader $reader) {
		$this->reader = $reader;
	}

}

