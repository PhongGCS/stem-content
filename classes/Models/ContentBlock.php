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
abstract class ContentBlock {
	//region Member variables
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
	protected $context = null;

	/**
	 * If on a Post page, this would be the post for that page.
	 * @var Post|null
	 */
	protected $post = null;

	/**
	 * The page that this content exists on, may be null.
	 * @var Page|null
	 */
	protected $page = null;

	/**
	 * The content data
	 * @var null|array
	 */
	protected $data = null;

	/**
	 * The content properties
	 * @var ContentBlockProperties
	 */
	protected $props;



	//endregion

	//region Constructor
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
		$this->data = $data;
		$this->props = new ContentBlockProperties($data);

		$userTemplate = arrayPath($data, 'template', null);

		if ($userTemplate) {
			$this->template = $userTemplate;
		} else if (!empty($template)) {
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

	//endregion

	//region Magic
	public function __get($name) {
		return $this->props->__get($name);
	}

	public function __isset($name) {
		return $this->props->__isset($name);
	}
	//endregion

	//region Properties
	/**
	 * Returns the content identifier for this block
	 * @return null|string
	 */
	public static function identifier() {
		return null;
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
	 * If on a Post page, this would be the post for that page.
	 * @return Post|null
	 */
	public function post() {
		return $this->post;
	}

	/**
	 * The page that this content exists on, may be null.
	 * @return Page|null
	 */
	public function page() {
		return $this->page;
	}

	/**
	 * The context of the page
	 * @return Context|null
	 */
	public function context() {
		return $this->context;
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

	//endregion

	//region Utility Functions

	/**
	 * Turns an ID in the content data into a Post object
	 * @param $data
	 * @param $field
	 *
	 * @return Post|null
	 */
	protected function parsePost($data, $field) {
		$pid = arrayPath($data, $field, null);
		if (!empty($pid)) {
			if (is_numeric($pid)) {
				return $this->context->modelForPostID($pid);
			} else if ($pid instanceof \WP_Post) {
				return $this->context->modelForPost($pid);
			}
		}

		return null;
	}
	//endregion

	//region Rendering

	/**
	 * Allows subclasses to add additional view data when rendering
	 *
	 * @param array $data
	 * @return array
	 */
	protected function additionalViewData($data) {
		return $data;
	}

	/**
	 * Renders the content type.  If you support partial rendering in your content block, you need to override this.
	 *
	 * @param bool $partial
	 * @param array|null $otherData
	 *
	 * @return string
	 */
	public function render($partial=false, $otherData=null) {
		if (!$this->template)
			return '';

		$data = ['content' => $this, 'partial' => $partial];
		if ($otherData) {
			$data=array_merge($data, $otherData);
		}

		$data = $this->additionalViewData($data);

		return $this->context->ui->render($this->template, $data);
	}
	//endregion
}