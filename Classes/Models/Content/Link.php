<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;
use ILab\StemContent\Traits\Content\HasLink;
use ILab\StemContent\Traits\Content\HasLinkInterface;

/**
 * Class Link
 *
 * A simple link
 *
 * @package ILab\StemContent\Models\Content
 */
class Link implements HasLinkInterface {
	use HasLink;

	public function __construct(Context $context, $data = null) {
		$this->parseLinkFromData($data, $context);
	}

	public function containerCSS() {
		return $this->containerCSS;
	}
}