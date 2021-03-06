<?php

namespace Cached;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

/**
 * @author Radek Ježdík
 */
class CachedExtension extends CompilerExtension {


	public function loadConfiguration() {
		$config = $this->getConfig();
		
		$profiles = array();
		if(isset($config['profiles']) && is_array($config['profiles'])) {
			$profiles = $config['profiles'];
		}
		
		$enabled = true;
		if(isset($config['enabled'])) {
			$enabled = (bool) $config['enabled'];
		}

		$this->getContainerBuilder()->addDefinition($this->prefix('cacheProfile'))
				->setClass('Cached\CacheProfileImpl', array('profiles' => $profiles), $enabled);
	}


	public static function register(Configurator $configurator) {
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
					$compiler->addExtension('cachable', new CachableExtension());
				};
	}

}