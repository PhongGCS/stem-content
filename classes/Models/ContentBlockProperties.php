<?php
namespace Stem\Content\Models;

use Carbon\Carbon;
use Stem\Core\Context;
use Stem\Models\Post;

class ContentBlockProperties {
	protected $data = [];
	protected $props = [];

	/**
	 * ContentBlockProperties constructor.
	 *
	 * @param array $data
	 */
	public function __construct($data) {
		$this->data = $data;
	}

	//region Add Properties
	/**
	 * Adds a number
	 * @param string $name
	 * @param float|null $defaultValue
	 */
	public function addFloat($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);
		$this->props[camelCaseString($name)] = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT);
	}

	/**
	 * Adds a number
	 * @param string $name
	 * @param integer|null $defaultValue
	 */
	public function addInteger($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);
		$this->props[camelCaseString($name)] = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * Adds a string
	 * @param string $name
	 * @param string|null $defaultValue
	 */
	public function addString($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);
		$this->props[camelCaseString($name)] = $val;
	}

	/**
	 * Adds a date
	 * @param string $name
	 * @param Carbon|null $defaultValue
	 */
	public function addDate($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);
		if (!empty($val)) {
			$val = Carbon::parse($val);
		}

		$this->props[camelCaseString($name)] = $val;
	}

	/**
	 * Adds a boolean
	 * @param string $name
	 * @param bool|null $defaultValue
	 */
	public function addBool($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);
		$this->props[camelCaseString($name)] = !empty($val);
	}

	/**
	 * Adds a post type
	 * @param string $name
	 * @param Post|null $defaultValue
	 */
	public function addPostType($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);

		$model = null;
		if (!empty($val)) {
			if (is_numeric($val)) {
				$model = Context::current()->modelForPostID($val);
			} else if ($val instanceof \WP_Post) {
				$model = Context::current()->modelForPost($val);
			} else if ($val instanceof Post) {
				$model = $val;
			}
		}

		$this->props[camelCaseString($name)] = $model;
	}

	/**
	 * Adds an array of items
	 * @param string $name
	 * @param null|callable $itemCallback
	 */
	public function addArray($name, $itemCallback) {
		$array = [];
		$val = arrayPath($this->data, $name, []) ?: [];

		if (empty($itemCallback)) {
			$array = $val;
		} else {
			foreach($val as $valItem) {
				$array[] = $itemCallback($valItem);
			}
		}

		$this->props[camelCaseString($name)] = $array;
	}
	//endregion

	//region Magic
	public function __get($name) {
		if (isset($this->props[$name])) {
			return $this->props[$name];
		}

		trigger_error("Invalid property name '$name'.", E_USER_ERROR);
	}

	public function __isset($name) {
		return isset($this->props[$name]);
	}
	//endregion
}