<?php
namespace ILab\StemContent\Traits\Content;

use Stem\Core\Context;
use Stem\Models\Page;

/**
 * Interface HasHeroSliderInterface
 * @package ILab\StemContent\Traits\Content
 */
interface HasHeroSliderInterface {
	/**
	 * Builds the hero slider content for the page.
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