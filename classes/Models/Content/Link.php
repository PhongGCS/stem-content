<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use Stem\Core\Context;
use Stem\Models\Page;
use Stem\Models\Post;
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

	public function __construct(Context $context, $data = null, $prefix = '') {
		$this->parseLinkFromData($data, $context, $prefix);
	}

	public function containerCSS() {
		return $this->containerCSS;
	}
}