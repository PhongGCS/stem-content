<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;
use ILab\StemContent\Traits\Content\HasLink;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

/**
 * Class PageHero
 *
 * Page hero's are the big image and headline elements on the top of the page.
 *
 * @package ILab\StemContent\Models\Content
 */
class PageHero extends ContentBlock {
	use HasLink;

	protected $backgroundImage = null;
	protected $image = null;
	protected $title = null;
	protected $text = null;

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/page-hero';

		parent::__construct($context, $data, $post, $page, $template);

		$bgImageId = arrayPath($data,'hero_background_image', null);
		if ($bgImageId) {
			$this->backgroundImage = $context->modelForPostID($bgImageId);
		}

		$imageId = arrayPath($data,'hero_image', null);
		if ($imageId) {
			$this->image = $context->modelForPostID($imageId);
		}

		$this->title = arrayPath($data, 'hero_title', null);
		$this->text = arrayPath($data, 'hero_text', null);

		$this->parseLink($page->id, $context, 'hero_');
	}

	/**
	 * The background image.
	 * @return \ILab\Stem\Models\Attachment|null
	 */
	public function backgroundImage() {
		return $this->backgroundImage;
	}

	/**
	 * Foreground image
	 * @return \ILab\Stem\Models\Attachment|null
	 */
	public function image() {
		return $this->image;
	}

	/**
	 * Title of the hero
	 * @return string|null
	 */
	public function title() {
		return $this->title;
	}

	/**
	 * Text of the hero
	 * @return string|null
	 */
	public function text() {
		return $this->text;
	}

}