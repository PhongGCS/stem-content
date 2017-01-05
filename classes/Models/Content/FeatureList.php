<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

/**
 * Class FeatureList
 *
 * A basic list of feature bullet points
 *
 * @package ILab\StemContent\Models\Content
 */
class FeatureList extends ContentBlock {
	protected $features = [];

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/feature-list';

		parent::__construct($context, $data, $post, $page, $template);

		$features = arrayPath($data, 'features', []);
		foreach($features as $feature)
			$this->features[] = new FeatureListItem($context, $feature);
	}

	public function features() {
		return $this->features;
	}
}