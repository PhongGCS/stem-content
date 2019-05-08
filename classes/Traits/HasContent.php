<?php

namespace Stem\Content\Traits;

use Stem\Content\Models\ContentBlock;
use Stem\Core\Context;
use Stem\Core\Response;
use Stem\Models\Page;
use Stem\Content\Models\ContentBlockContainer;

/**
 * Class HasLink
 *
 * Trait for content that has a link clone field attached to it.
 *
 * @package Stem\Content\Traits\Content
 */
trait HasContent {
	/**
	 * Content container
	 * @var ContentBlockContainer
	 */
	protected $content = null;

	/**
	 * Builds the content for the page.
	 *
	 * @param Context $context
	 * @param Page $page
	 */
	public function buildContent(Context $context, Page $page) {
		$contentData = get_field("content", $page->id);
		$this->content = new ContentBlockContainer($context, $contentData, null, $page);
	}

	public function renderContent($partial, $template, $otherData) {
		if ($partial) {
			$result='';

			/** @var ContentBlock $content */
			foreach($this->content->content() as $content) {
				if ($content->supportsPartial($partial)) {
					$result .= $content->render(true);
				}
			}

			return new \Symfony\Component\HttpFoundation\Response($result);
		} else {
			$data = [
				'content' => $this->content->content()
			];

			if (is_array($otherData)) {
				$data = array_merge($otherData, $data);
			}

			$res = new Response($template, $data);
			return $res;
		}
	}
}