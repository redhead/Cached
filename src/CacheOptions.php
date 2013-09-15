<?php

namespace Cached;

/**
 * The data container for cache options.
 *
 * @author Radek Ježdík
 *
 * @property-read string $namespace the cache namespace
 * @property-read bool $enabled true if caching is enabled
 * @property-read array $dependencies the cache dependencies
 */
class CacheOptions extends \Nette\Object {

	private $namespace;

	private $enabled;

	private $dependencies;


	/**
	 * @param string $namespace
	 * @param bool $enabled
	 * @param array $dependencies
	 */
	function __construct($namespace, $enabled, $dependencies) {
		$this->namespace = (string) $namespace;
		$this->enabled = (bool) $enabled;
		$this->dependencies = (array) $dependencies;
	}


	/**
	 * @return string
	 */
	public function getNamespace() {
		return $this->namespace;
	}


	/**
	 * @return bool
	 */
	public function getEnabled() {
		return $this->enabled;
	}


	/**
	 * @return array
	 */
	public function getDependencies() {
		return $this->dependencies;
	}

}

