<?php

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

class CachingAspectTest extends BaseTestCase {


	public function testUsingDefaultProfileIfNoneSpecified() {
		$cacheProfile = new Cached\CacheProfileImpl(array(
			'default' => array(
				'sliding' => true,
				'namespace' => "defaultProfile",
				"expire" => "10",
			)
		));

		$actualOptions = $cacheProfile->getOptions(new Cached\Cached());

		$expectedDependencies = array(
			"sliding" => true,
			"expire" => "10"
		);

		Assert::equal('defaultProfile', $actualOptions->namespace);
		Assert::true($actualOptions->enabled);
		Assert::equal($expectedDependencies, $actualOptions->dependencies);
	}


	public function testOverridingProfileOptions() {
		$cacheProfile = new Cached\CacheProfileImpl(array(
			'test' => array(
				'expire' => "4",
				'files' => array('file1'),
				'namespace' => 'namespace1',
				'priority' => 1,
				'sliding' => false,
				'tags' => array('tag1')
			)
		));

		$ann = new Cached\Cached();
		$ann->profile = 'foo';

		$ann->expire = "5";
		$ann->files = array('file2');
		$ann->namespace = 'namespace2';
		$ann->priority = 2;
		$ann->sliding = true;
		$ann->tags = array('tag2');

		$actualOptions = $cacheProfile->getOptions($ann);

		$expectedDependencies = array(
			'expire' => "5",
			'files' => array('file2'),
			'priority' => 2,
			'sliding' => true,
			'tags' => array('tag2')
		);

		Assert::equal('namespace2', $actualOptions->namespace);
		Assert::true($actualOptions->enabled);
		Assert::equal($expectedDependencies, $actualOptions->dependencies);
	}

}

(new CachingAspectTest())->run();