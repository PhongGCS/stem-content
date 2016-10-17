<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

/**
 * Class Text
 *
 * A basic text block
 *
 * @package ILab\StemContent\Models\Content
 */
class Text extends ContentBlock {
	protected $text = null;
	protected $containerCSS = null;

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/text';

		parent::__construct($context, $data, $post, $page, $template);

		$this->text = arrayPath($data, 'text', null);
		$this->containerCSS = arrayPath($data, 'container_css', null);
	}

	public function text() {
		return $this->text;
	}

	public function containerCSS() {
		return $this->containerCSS;
	}
}