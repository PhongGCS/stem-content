<?php
namespace Stem\Content\Models;

use Stem\Core\Context;
use Stem\Models\Page;
use Stem\Models\Post;
use Stem\Models\User;
use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Class ContentBlock
 *
 * Base type for content blocks
 *
 * @package Stem\Content\Models
 */
abstract class ContentBlock {
	//region Class variables

	/** @var string[] Property types  */
	protected static $propertyTypes = [];

	//endregion

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

		if (empty($this->template)) {
			$this->template = 'content.'.static::identifier();
		}

		$this->page = $page;
		$this->post = $post;
		$this->context = $context;

		$cssClasses = arrayPath($data, 'container_css', null);
		if ($cssClasses && is_array($cssClasses)) {
			$this->containerCSS = implode(' ', $cssClasses);
		}

		$this->buildProperties();
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
	 * Returns the title for the content type
	 * @return null|string
	 */
	public static function title() {
		return null;
	}

	/**
	 * Allows subclasses to configure their ACF fields in code.  Don't worry about specifying the location
	 * element, it will be added automatically if it is missing.
	 *
	 * @return FieldsBuilder|null
	 */
	public static function buildFields() {
		return null;
	}

	public static function updatePropertyTypes($fields) {
		$propTypes = [];

		foreach($fields['fields'] as $field) {
			$type = $field['type'];
			$name = $field['name'];

			if (($type == 'number') || ($type == 'range')) {
				$propTypes[$name] = 'float';
			} else if ($type == 'true_false') {
				$propTypes[$name] = 'bool';
			} else if ($type == 'checkbox') {
				$propTypes[$name] = 'strings';
			} else if (($type == 'gallery') || ($type == 'relationship')) {
				$propTypes[$name] = 'objects';
			} else if ($type == 'link') {
				$propTypes[$name] = 'link';
			} else if (in_array($type, ['image', 'file', 'post_object'])) {
				$propTypes[$name] = 'object';
			} else if (in_array($type, ['date_picker', 'date_time_picker'])) {
				$propTypes[$name] = 'date';
			} else if ($type == 'user') {
				$propTypes[$name] = 'user';
			}  else if (($type == 'taxonomy') && isset($field['taxonomy'])) {
				if (!empty($field['multiple'])) {
					$propTypes[$name] = 'taxonomies|'.$field['taxonomy'];
				} else {
					$propTypes[$name] = 'taxonomy|'.$field['taxonomy'];
				}
			} else if ($type == 'repeater') {
				if (isset($field['repeater_item_class'])) {
					$propTypes[$name] = 'array|'.$field['repeater_item_class'];
				} else {
					$propTypes[$name] = 'array';
				}
			} else {
				$propTypes[$name] = 'string';
			}
		}

		static::$propertyTypes[static::class] = $propTypes;
	}

	protected function buildProperties() {
		if (!isset(static::$propertyTypes[static::class])) {
			return;
		}

		$propTypes = static::$propertyTypes[static::class];

		foreach($propTypes as $name => $type) {
			if ($type == 'float') {
				$this->props->addFloat($name);
			} else if ($type == 'string') {
				$this->props->addString($name);
			} else if ($type == 'strings') {
				$this->props->addArray($name, function($item) {
					return $item;
				});
			} else if ($type == 'bool') {
				$this->props->addBool($name);
			} else if ($type == 'date') {
				$this->props->addDate($name);
			} else if (strpos($type, 'taxonomy|') === 0) {
				$tax = str_replace('taxonomy|', '', $type);
				$this->props->addTaxonomy($name, $tax);
			} else if (strpos($type, 'taxonomies|') === 0) {
				$tax = str_replace('taxonomies|', '', $type);
				$this->props->addTaxonomies($name, $tax);
			} else if ($type == 'objects') {
				$this->props->addArray($name, function($item) {
					if (is_array($item) && isset($item['ID'])) {
						return $this->context->modelForPostID($item['ID']);
					} else if (is_numeric($item)) {
						return $this->context->modelForPostID($item);
					} else if ($item instanceof \WP_Post) {
						return $this->context->modelForPost($item);
					} else if ($item instanceof Post) {
						return $item;
					}

					return null;
				});
			} else if ($type == 'object')  {
				$this->props->addPostType($name);
			} else if ($type == 'link')  {
				$this->props->addLink($name);
			} else if ($type == 'array') {
				$this->props->addArray($name, function($item) {
					if (is_array($item)) {
						return (object)$item;
					}

					return $item;
				});
			}  else if (strpos($type, 'array|') === 0) {
				$itemClass = str_replace('array|', '', $type);
				$this->props->addArray($name, function($item) use ($itemClass) {
					return new $itemClass($item);
				});
			} else if ($type == 'user') {
				$this->props->addTransformer($name, function($item) {
					if (is_numeric($item)) {
						$item = get_user_by('id', $item);
					} else if (is_array($item)) {
						if (isset($item['id'])) {
							$item = get_user_by('id', $item['id']);
						} else if (isset($item['ID'])) {
							$item = get_user_by('id', $item['ID']);
						} else {
							return null;
						}
					}

					if ($item instanceof \WP_User) {
						return new User($this->context, $item);
					}

					return null;
				});
			}
		}
	}

	/**
	 * The content targets that this block is applicable to
	 * @return array
	 */
	public static function contentTargets() {
		return ['main_content'];
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