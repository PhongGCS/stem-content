<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use Stem\Core\Context;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class LinkList
 *
 * A basic list of links
 *
 * @package ILab\StemContent\Models\Content
 */
class LinkList extends ContentBlock {
	protected $links = [];

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = 'partials/content/link-list';

		parent::__construct($context, $data, $post, $page, $template);

		$links = arrayPath($data, 'links', []);
		foreach($links as $link)
			$this->links[] = new Link($context, $link);
	}

	public function links() {
		return $this->links;
	}
}