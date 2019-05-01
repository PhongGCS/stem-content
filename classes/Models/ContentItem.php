<?php

namespace Stem\Content\Models;

abstract class ContentItem {
	/**
	 * The content properties
	 * @var ContentBlockProperties
	 */
	protected $props;

	public function __construct($data = null) {
		$this->props = new ContentBlockProperties($data);
	}

	//region Magic
	public function __get($name) {
		return $this->props->__get($name);
	}

	public function __isset($name) {
		return $this->props->__isset($name);
	}
	//endregion
}