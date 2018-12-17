<?php
namespace ILab\StemContent\Models;

use Stem\Core\Context;
use Stem\Core\Log;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class ContentBlockContainer
 *
 * Container for all content blocks on a given page.  This required the use of ACF Pro's flexible content.
 *
 * @package ILab\StemContent\Models
 */
class ContentBlockContainer {
	/**
	 * List of content blocks
	 * @var array
	 */
	public $content=[];

	/**
	 * Context
	 * @var Context|null
	 */
	public $context=null;

	/**
	 * ContentBlockContainer constructor.
	 *
	 * @param Context $context
	 * @param $contentData
	 * @param Post|null $post
	 * @param Page|null $page
	 */
	public function __construct(Context $context, $contentData, Post $post=null, Page $page=null){
		$this->context = $context;

		if ($contentData && is_array($contentData)) {
			/** @var ContentBlock $previousContentBlock */
			$previousContentBlock = null;
			foreach($contentData as $contentObj) {
				if (!isset($contentObj["acf_fc_layout"]))
					continue;

				$contentType = $contentObj["acf_fc_layout"];
				$contentTypeClass = $context->ui->setting("content/map/{$contentType}");
				if (class_exists($contentTypeClass)) {
					/** @var ContentBlock $contentBlock */
					$contentBlock = new $contentTypeClass($context, $contentObj, $post, $page);
					if ($previousContentBlock != null) {
						$previousContentBlock->setNextContentBlock($contentBlock);
					}

					$this->content[] = $contentBlock;

					$previousContentBlock = $contentBlock;
				} else {
					Log::warning("$contentTypeClass does not exist for $contentType");
				}
			}
		}
	}
}