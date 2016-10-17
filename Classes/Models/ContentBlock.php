<?php
namespace ILab\StemContent\Models;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

/**
 * Class ContentBlock
 *
 * Base type for content blocks
 *
 * @package ILab\StemContent\Models
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
		$this->template = $template;
		$this->page = $page;
		$this->post = $post;
		$this->context = $context;

		$this->containerCSS = arrayPath($data, 'container_css', null);
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
}