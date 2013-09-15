<?php

use Tester\Assert;
use Mockyll\Match as Arg;

require __DIR__ . '/bootstrap.php';

class CacheDelegateTest extends BaseTestCase {

	const NAME_SPACE = 'myNamespace';
	const VALUE = 'foo';


	protected function setUp() {
		parent::setUp();

		$this->dependencies = array(
			'sliding' => true,
			'expire' => 42
		);

		$this->storageMock = $this->mock('Nette\Caching\IStorage');
	}


	private function createCache() {
		$options = new \Cached\CacheOptions(self::NAME_SPACE, true, $this->dependencies);
		$cache = new \Cached\CacheDelegate($this->storageMock, 'keyGetsHashed', $options);

		return $cache;
	}


	public function testSavingToCacheStorage() {
		$this->storageMock->write()->match(
				Arg::has(self::NAME_SPACE), Arg::is(self::VALUE), Arg::is($this->dependencies)
		);

		return function() {
					$cache = $this->createCache();
					$cache->save(self::VALUE);
				};
	}


	public function testHasValue() {
		$this->storageMock->read()->match(Arg::has(self::NAME_SPACE))->returns(self::VALUE);

		return function() {
					$cache = $this->createCache();
					Assert::true($cache->hasValue());
				};
	}


	public function testGetValue() {
		$this->storageMock->read()->match(Arg::has(self::NAME_SPACE))->returns(self::VALUE);

		return function() {
					$cache = $this->createCache();
					Assert::equal(self::VALUE, $cache->getValue());
				};
	}

}

(new CacheDelegateTest())->run();