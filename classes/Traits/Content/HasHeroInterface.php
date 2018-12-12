<?php
namespace ILab\StemContent\Traits\Content;

use Stem\Core\Context;
use Stem\Models\Page;

/**
 * Interface HasHeroInterface
 * @package ILab\StemContent\Traits\Content
 */
interface HasHeroInterface {
	/**
	 * Builds the hero content for the page.
	 *
	 * @param Context $context
	 * @param Page $page
	 */
	public function buildHero(Context $context, Page $page);

	/**
	 * Returns the page hero if any.
	 * @return PageHero|null
	 */
	public function hero();
}