<?php
namespace ILab\StemContent\Models;

use ILab\Stem\Core\Context;
use ILab\Stem\Core\Log;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

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
			foreach($contentData as $contentObj) {
				if (!isset($contentObj["acf_fc_layout"]))
					continue;

				$contentType = $contentObj["acf_fc_layout"];
				$contentTypeClass = $context->ui->setting("content/map/{$contentType}");
				if (class_exists($contentTypeClass)) {
					$this->content[] = new $contentTypeClass($context, $contentObj, $post, $page);
				} else {
					Log::warning("$contentTypeClass does not exist for $contentType");
				}
			}
		}
	}
}