<?php
namespace Stem\Content\Models;

use Stem\Core\Context;
use Stem\Models\Attachment;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class ContentBlock
 *
 * Base type for content blocks
 *
 * @package Stem\Content\Models
 */
class ContentBlock {
	/**
	 * The name of the template that this content block uses to render.
	 * @var null|string
	 */
	protected $template = null;

	/**
	 * User supplied container CSS class
	 * @var string|null
	 */
	protected $containerCSS = null;

	/**
	 * The previous content block
	 * @var null|ContentBlock
	 */
	protected $previousBlock = null;

	/**
	 * The next content block
	 * @var null|ContentBlock
	 */
	protected $nextBlock = null;


	/**
	 * Context
	 * @var Context|null
	 */
	public $context = null;

	/**
	 * This flag indicates that the front end is requesting a "partial" render, meaning that this content block should
	 * render itself in a special way as to only include the elements that can be appended to the DOM on the front end.
	 * For example, if this content block were a list of posts, a "partial" render would only return the individual
	 * posts elements, not the container that houses them.
	 * @var bool
	 */
	public $partial = false;

	/**
	 * If on a Post page, this would be the post for that page.
	 * @var Post|null
	 */
	public $post = null;

	/**
	 * The page that this content exists on, may be null.
	 * @var Page|null
	 */
	public $page = null;

	/**
	 * ContentBlock constructor.
	 *
	 * @param Context $context
	 * @param null $data
	 * @param Post|null $post
	 * @param Page|null $page
	 * @param null $template
	 */
	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		$userTemplate = arrayPath($data, 'template', null);

		if ($userTemplate) {
			$this->template = $userTemplate;
		} else {
			$this->template = $template;
		}

		$this->page = $page;
		$this->post = $post;
		$this->context = $context;

		$cssClasses = arrayPath($data, 'container_css', null);
		if ($cssClasses && is_array($cssClasses)) {
			$this->containerCSS = implode(' ', $cssClasses);
		}
	}

	/**
	 * Determines if this content block supports partial rendering for a given key that is passed to the back end
	 * by an ajax request on the front end.  For example, if this content block rendered a list of posts, it would
	 * support partial rendering for requests with the key 'post-list' for infinite scrolling type of jam.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function supportsPartial($key) {
		return false;
	}

	/**
	 * Turns an ID in the content data into an Attachment object
	 * @param $data
	 * @param $field
	 *
	 * @return Attachment|null
	 */
	protected function parseImage($data, $field) {
		$imageID = arrayPath($data, $field, null);
		if (!empty($imageID)) {
			if (is_numeric($imageID)) {
				return $this->context->modelForPostID($imageID);
			} else if ($imageID instanceof \WP_Post) {
				return $this->context->modelForPost($imageID);
			}
		}


		return null;
	}

	/**
	 * Renders the content type.  If you support partial rendering in your content block, you need to override this.
	 *
	 * @param null $otherData
	 *
	 * @return string
	 */
	public function render($otherData=null) {
		if (!$this->template)
			return '';

		$data = ['content' => $this];
		if ($otherData)
			$data=array_merge($data, $otherData);

		return $this->context->ui->render($this->template, $data);
	}

	/**
	 * Container CSS class specified by the end user for this content block
	 * @return null|string
	 */
	public function containerCSS() {
		return $this->containerCSS;
	}

	/**
	 * Sets the previous content block
	 * @param $previous ContentBlock
	 */
	public function setPreviousContentBlock($previous) {
		$this->previousBlock = $previous;
		$previous->nextBlock = $this;
	}

	/**
	 * Sets the next content block
	 * @param $next ContentBlock
	 */
	public function setNextContentBlock($next) {
		$this->nextBlock = $next;
		$next->previousBlock = $this;
	}

}