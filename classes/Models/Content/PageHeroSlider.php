<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use Stem\Core\Context;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class PageHeroSlider
 *
 * A hero slider.
 *
 * @package ILab\StemContent\Models\Content
 */
class PageHeroSlider extends ContentBlock {
	protected $heroes = [];

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/page-hero-slider';

		parent::__construct($context, $data, $post, $page, $template);

		$allHeroesData = arrayPath($data,'heroes', []);
		foreach($allHeroesData as $heroData)
			$this->heroes[] = new PageHero($context, $heroData, $post, $page, null);
	}

	/**
	 * The list of PageHero content blocks in this slider.
	 * @return array
	 */
	public function heroes() {
		return $this->heroes;
	}

}