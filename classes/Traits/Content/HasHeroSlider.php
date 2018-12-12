<?php
namespace ILab\StemContent\Traits\Content;

use ILab\StemContent\Models\Content\PageHero;

use Stem\Core\Context;
use Stem\Models\Page;
use ILab\StemContent\Models\Content\PageHeroSlider;

/**
 * Trait HasHeroSlider
 *
 * Trait for pages that have PageHeroSlider content
 *
 * @package ILab\StemContent\Traits\Content
 */
trait HasHeroSlider {
	/**
	 * The page hero slider content block
	 * @var PageHeroSlider
	 */
	public $heroSlider = null;

	/**
	 * Builds the hero slider content for the page.
	 *
	 * @param Context $context
	 * @param Page $page
	 */
	public function buildHero(Context $context, Page $page) {
		$heroes = get_field('heroes', $page->id, []);
		$css = get_field('hero_container_css', $page->id);

		$this->heroSlider = new PageHeroSlider($context, ['heroes' => $heroes, 'container_css' => $css], null, $page);
	}

	/**
	 * Returns the page hero if any.
	 * @return PageHero|null
	 */
	public function hero() {
		return $this->hero;
	}
}