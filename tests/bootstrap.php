<?php

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

if(extension_loaded('xdebug')) {
	Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}

class BaseTestCase extends Tester\TestCase {

	/**
	 * @var Mockyll\MockController
	 */
	protected $mockCtrl;


	protected function setUp() {
		parent::setUp();
		$this->mockCtrl = new Mockyll\MockController();
	}


	public function runTest($name, array $args = array()) {
		$this->setUp();
		try {
			$return = call_user_func_array(array($this, $name), $args);
			if(is_callable($return)) {
				$this->mockCtrl->play($return);
			}
		} catch(\Exception $e) {
		}
		$this->tearDown();
		if(isset($e)) {
			throw $e;
		}
	}


	protected function mock($class, $methods = array()) {
		return $this->mockCtrl->mock($class, $methods);
	}


	protected function partial($class, $methods = array(), $constructorArgs = null) {
		return $this->mockCtrl->partial($class, $methods, $constructorArgs);
	}

}