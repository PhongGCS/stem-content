<?php
namespace Stem\Content\Models;

use Stem\Core\Context;
use Stem\Core\Log;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class ContentBlockContainer
 *
 * Container for all content blocks on a given page.  This required the use of ACF Pro's flexible content.
 *
 * @package Stem\Content\Models
 */
class ContentBlockContainer {

	/**
	 * List of content blocks
	 * @var array
	 */
	protected $content = [];

	/**
	 * Context
	 * @var Context|null
	 */
	protected $context = null;

	/**
	 * ContentBlockContainer constructor.
	 *
	 * @param Context $context
	 * @param $contentData
	 * @param Post|null $post
	 * @param Page|null $page
	 */
	public function __construct(Context $context, $contentData, Post $post = null, Page $page = null) {
		$this->context = $context;

		if($contentData && is_array($contentData)) {
			$blockClasses = $context->ui->setting('content/blocks', []);
			$blockClasses = apply_filters('heavymetal/ui/content/blocks', $blockClasses);

			$contentBlockMap = [];
			foreach($blockClasses as $blockClass) {
				$contentBlockMap[$blockClass::identifier()] = $blockClass;
			}

			/** @var ContentBlock $previousContentBlock */
			$previousContentBlock = null;
			foreach($contentData as $contentObj) {
				if(!isset($contentObj["acf_fc_layout"])) continue;

				$contentType = $contentObj["acf_fc_layout"];
				if(isset($contentBlockMap[$contentType])) {
					$contentTypeClass = $contentBlockMap[$contentType];
					if (class_exists($contentTypeClass)) {
						/** @var ContentBlock $contentBlock */
						$contentBlock = new $contentTypeClass($context, $contentObj, $post, $page);
						if($previousContentBlock != null) {
							$previousContentBlock->setNextContentBlock($contentBlock);
						}

						$this->content[] = $contentBlock;

						$previousContentBlock = $contentBlock;
					} else {
						Log::warning("Content block class '$contentTypeClass' does not exist.");
					}
				} else {
					Log::warning("Content type '$contentType' does not exist.");
				}
			}
		}
	}

	/**
	 * The content contained in this container
	 * @return ContentBlock[]
	 */
	public function content() {
		return $this->content;
	}
}