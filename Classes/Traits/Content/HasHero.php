<?php
namespace ILab\StemContent\Traits\Content;

use ILab\StemContent\Models\Content\PageHero;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;

/**
 * Class HasHero
 *
 * Trait for pages that have PageHero content
 *
 * @package ILab\StemContent\Traits\Content
 */
trait HasHero {
	/**
	 * The hero content block
	 * @var PageHero
	 */
	protected $hero = null;

	/**
	 * Builds the hero content for the page.
	 *
	 * @param Context $context
	 * @param Page $page
	 */
	public function buildHero(Context $context, Page $page) {
		$heroData=[
			'hero_background_image' => get_field('hero_background_image', $page->id),
			'hero_image' => get_field('hero_image', $page->id),
			'hero_title' => get_field('hero_title', $page->id),
			'hero_text' => get_field('hero_text', $page->id)
		];

		$this->hero = new PageHero($context, $heroData, null, $page);
	}

	/**
	 * Returns the page hero if any.
	 * @return PageHero|null
	 */
	public function hero() {
		return $this->hero;
	}
}