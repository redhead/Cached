<?php

namespace Cached;

use Nette\Caching\Cache;

/**
 * Holds profiles and creates
 *
 * @author Radek Ježdík <jezdik.radek@gmail.com>
 * @internal
 */
class CacheProfileImpl implements ICacheProfile {

	private $profiles;

	private $enabled;

	public function __construct(array $profiles, $enabled = true) {
		$this->profiles = $profiles;
		$this->enabled = (bool) $enabled;
	}


	public function getOptions(Cached $ann) {
		$profile = $ann->profile ? : 'default';

		$options = array(
			'namespace' => $profile,
			'enabled' => $this->enabled
		);

		if(isset($this->profiles[$profile])) {
			foreach($this->profiles[$profile] as $name => $value) {
				$options[$name] = $value;
			}
		}

		return $this->merge($options, $ann);
	}


	private function merge(array $options, Cached $ann) {
		$namespace = $ann->namespace ? : $options['namespace'];

		$dependencies = array();
		
		$this->mergeOption($dependencies, $options, $ann, Cache::EXPIRE);
		$this->mergeOption($dependencies, $options, $ann, Cache::TAGS);
		$this->mergeOption($dependencies, $options, $ann, Cache::FILES);
		$this->mergeOption($dependencies, $options, $ann, Cache::PRIORITY);
		$this->mergeOption($dependencies, $options, $ann, Cache::SLIDING);

		return new CacheOptions($namespace, $options['enabled'], $dependencies);
	}


	private function mergeOption(&$target, array $options, Cached $ann, $name) {
		if(isset($options[$name])) {
			$target[$name] = $options[$name];
		}
		if($ann->$name) {
			$target[$name] = $ann->$name;
		}
	}

}