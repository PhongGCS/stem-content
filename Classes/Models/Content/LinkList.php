<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

/**
 * Class LinkList
 *
 * A basic list of links
 *
 * @package ILab\StemContent\Models\Content
 */
class LinkList extends ContentBlock {
	protected $links = [];
	protected $containerCSS = null;

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/link-list';

		parent::__construct($context, $data, $post, $page, $template);

		$links = arrayPath($data, 'links', []);
		foreach($links as $link)
			$this->links[] = new Link($context, $link);

		$this->containerCSS = arrayPath($data, 'container_css', null);
	}

	public function links() {
		return $this->links;
	}

	public function containerCSS() {
		return $this->containerCSS;
	}
}