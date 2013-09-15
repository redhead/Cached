<?php

namespace Cached;

interface ICacheProfile {

	/**
	 * @param \Cached\Cached $ann
	 * @return \Cached\CacheOptions the caching options
	 */
	public function getOptions(Cached $ann);
	
}