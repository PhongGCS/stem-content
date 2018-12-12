<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use Stem\Core\Context;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class Text
 *
 * A basic text block
 *
 * @package ILab\StemContent\Models\Content
 */
class Title extends ContentBlock {
	protected $title = null;
	protected $subtitle = null;

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/title';

		parent::__construct($context, $data, $post, $page, $template);

		$this->title = arrayPath($data, 'title', null);
		$this->subtitle = arrayPath($data, 'subtitle', null);
	}

	public function title() {
		return $this->title;
	}

	public function subtitle() {
		return $this->subtitle;
	}
}