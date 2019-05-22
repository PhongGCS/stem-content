<?php

namespace Stem\Content\Models;

/**
 * Class ACFLink
 * @package Stem\Content\Models
 *
 * @property-read string $title
 * @property-read string $url
 * @property-read string $target
 */
class ACFLink extends ContentItem {
	public function __construct($data = null) {
		parent::__construct($data);

		$this->props->addString('title');
		$this->props->addString('url');
		$this->props->addString('target');
	}

	public function render($classes = []) {
		if (empty($this->url)) {
			return '';
		}

		if (is_string($classes)) {
			$classes = [$classes];
		}

		$cssClasses = '';
		if (is_array($classes) && !empty($classes)) {
			$cssClasses = implode(' ', $classes);
		}

		$attrs = [];
		if (!empty($this->target)) {
			$attrs['target'] = $this->target;
		}

		if (!empty($cssClasses)) {
			$attrs['class'] = $cssClasses;
		}

		$attrString = '';
		foreach($attrs as $key => $value) {
			$attrString .= "$key='$value' ";
		}

		return "<a href='{$this->url}' $attrString>{$this->title}</a>";
	}
}