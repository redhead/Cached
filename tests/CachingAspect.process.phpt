<?php

use Tester\Assert;
use Mockyll\Match as Arg;

require __DIR__ . '/bootstrap.php';

class AdvicedClass {


	public function method() {
		// return value is mocked in the tests
	}

}

class CachingAspectUnderTest extends Cached\CachingAspect {

	private $cacheDelegateMock;


	function __construct($cacheDelegateMock, $storage, $cacheProfiles) {
		parent::__construct($storage, $cacheProfiles);
		$this->cacheDelegateMock = $cacheDelegateMock;
	}


	public function createCache($key, \Cached\CacheOptions $options) {
		$this->cacheDelegateMock->_construct($key, $options);
		return $this->cacheDelegateMock;
	}

}

class CachingAspectTest extends BaseTestCase {

	const KEY = 'myKey';
	const VALUE = 'myValue';


	protected function setUp() {
		parent::setUp();

		$this->annotation = new Cached\Cached();
		$this->annotation->key = self::KEY;

		$this->cacheProfileMock = $this->mock('Cached\ICacheProfile');

		$this->cacheDelegateMock = $this->mock('Cached\CacheDelegate');

		$this->annReaderMock = $this->mock('Doctrine\Common\Annotations\AnnotationReader');
		$this->annReaderMock->getMethodAnnotation()
				->match(Arg::isOf('Nette\Reflection\Method'), Arg::is('Cached\Cached'))
				->returns($this->annotation);

		$this->joinpointMock = $this->partial('Kdyby\Aop\JoinPoint\AroundMethod', null, array(
			new AdvicedClass,
			'method'
		));
	}


	private function verify() {
		return function() {
			$storage = new Nette\Caching\Storages\DevNullStorage;

			$aspect = new CachingAspectUnderTest($this->cacheDelegateMock, $storage, $this->cacheProfileMock);
			$aspect->setReader($this->annReaderMock);

			$actualValue = $aspect->process($this->joinpointMock);

			Assert::equal(self::VALUE, $actualValue);
		};
	}


	public function testSavingToCacheStorage() {
		$options = new \Cached\CacheOptions(null, true, array());

		$this->cacheProfileMock->getOptions($this->annotation)->returns($options);

		$this->cacheDelegateMock->_construct(self::KEY, $options);
		$this->cacheDelegateMock->hasValue()->returns(false);
		$this->cacheDelegateMock->save(self::VALUE);

		$this->joinpointMock->proceed()->returns(self::VALUE);

		return $this->verify();
	}


	public function testRetrievingFromCacheStorage() {
		$options = new \Cached\CacheOptions(null, true, array());

		$this->cacheProfileMock->getOptions($this->annotation)->returns($options);

		$this->cacheDelegateMock->_construct(self::KEY, $options);
		$this->cacheDelegateMock->hasValue()->returns(true);
		$this->cacheDelegateMock->getValue()->returns(self::VALUE);

		return $this->verify();
	}


	public function testCallsMethodWhenProfileIsDisabled() {
		$options = new \Cached\CacheOptions(null, false, array());

		$this->cacheProfileMock->getOptions($this->annotation)->returns($options);

		$this->joinpointMock->proceed()->returns(self::VALUE);

		return $this->verify();
	}

}

(new CachingAspectTest())->run();