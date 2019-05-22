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
	 * Content targets
	 * @var ContentBlockContainer[]
	 */
	protected $contentTargets = [];

	/**
	 * Builds the content for the page.
	 *
	 * @param Context $context
	 * @param Page $page
	 */
	public function buildContent(Context $context, Page $page) {
		if ($context->ui->setting('content/pageContent/enabled', false)) {
			$this->processTargets($context, $page);
		} else {
			$contentData = get_field("content", $page->id) ?: [];
			$this->content = new ContentBlockContainer($context, $contentData, null, $page);
		}
	}

	protected function processTargets(Context $context, Page $page) {
		$targets = apply_filters('heavymetal/ui/content/pageContent/targets', []);
		$targets = array_merge($targets, $context->ui->setting('content/pageContent/targets', []));

		foreach($targets as $targetName => $target) {
			$contentData = get_field($targetName, $page->id) ?: [];
			$this->contentTargets[$targetName] = new ContentBlockContainer($context, $contentData, null, $page);
		}
	}

	public function renderContent($partial, $partialTarget, $template, $otherData) {
		if ($partial) {
			$result='';

			if (!empty($this->content)) {
				/** @var ContentBlock $content */
				foreach($this->content->content() as $content) {
					if ($content->supportsPartial($partial)) {
						$result .= $content->render(true);
					}
				}
			} else if (!empty($partialTarget) && isset($this->contentTargets[$partialTarget])) {
				$contentContainer = $this->contentTargets[$partialTarget];

				/** @var ContentBlock $content */
				foreach($contentContainer->content() as $content) {
					if ($content->supportsPartial($partial)) {
						$result .= $content->render(true);
					}
				}
			}

			return new \Symfony\Component\HttpFoundation\Response($result);
		} else {
			$data = [];

			if (!empty($this->content)) {
				$data['content'] = $this->content->content();
			} else {
				foreach($this->contentTargets as $key => $contentContainer) {
					$data[camelCaseString($key)] = $contentContainer->content();
				}
			}

			if (is_array($otherData)) {
				$data = array_merge($otherData, $data);
			}

			$res = new Response($template, $data);
			return $res;
		}
	}
}