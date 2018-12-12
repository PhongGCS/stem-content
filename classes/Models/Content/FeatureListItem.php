<?php

namespace ILab\StemContent\Models\Content;

use Stem\Core\Context;

/**
 * Class FeatureListItem
 *
 * An item in a feature list.
 *
 * @package ILab\StemContent\Models\Content
 */
class FeatureListItem {
	protected $context = null;

	protected $icon = null;
	protected $title = null;
	protected $description = null;
	
	public function __construct(Context $context, $data = null) {
		$this->context = $context;

		$iconImageID = arrayPath($data, 'icon', null);
		if ($iconImageID)
			$this->icon = $context->modelForPostID($iconImageID);

		$this->title = arrayPath($data, 'title', null);
		$this->description = arrayPath($data, 'description', null);
	}

	public function icon() {
		return $this->icon;
	}

	public function title() {
		return $this->title;
	}

	public function description() {
		return $this->description;
	}
}