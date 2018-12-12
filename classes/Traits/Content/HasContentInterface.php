<?php

namespace ILab\StemContent\Traits\Content;

use Stem\Core\Context;
use Stem\Models\Page;

/**
 * Interface HasContentInterface
 * @package ILab\StemContent\Traits\Content
 */
interface HasContentInterface {
	/**
	 * Builds the content for the page.
	 *
	 * @param Context $context
	 * @param Page $page
	 */

	public function buildContent(Context $context, Page $page);
}
