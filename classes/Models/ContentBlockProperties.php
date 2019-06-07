<?php
namespace Stem\Content\Models;

use Carbon\Carbon;
use Stem\Core\Context;
use Stem\Models\Post;
use Stem\Models\Taxonomy;
use Stem\Models\Term;

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
	 * Adds a multiline string as an array of strings, splitting on \n
	 * @param string $name
	 * @param string|null $defaultValue
	 */
	public function addMultilineString($name, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);

		if (!is_string($val)) {
			return;
		}

		$name = camelCaseString($name);
		$this->props[$name] = [];
		$vals = explode("\n", $val);
		array_walk($vals, function($item) use ($name) {
			if (!empty(trim($item))) {
				$this->props[$name][] = $item;
			}
		});
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
	 * Adds a taxonomy type
	 * @param string $name
	 * @param Post|null $defaultValue
	 */
	public function addTaxonomies($name, $taxonomy, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);

		if (!is_array($val)) {
			$val = [$val];
		}

		$models = [];
		foreach($val as $value) {
			$model = null;
			if (!empty($value)) {
				if (is_numeric($value)) {
					$model = Term::term(Context::current(), $value, $taxonomy);
				} else if ($value instanceof \WP_Term) {
					$model = Term::termFromTermData(Context::current(), $value->to_array());
				} else if (is_array($value)) {
					$model = Term::termFromTermData(Context::current(), $value);
				}
			}

			if (!empty($model)) {
				$models[] = $model;
			}
		}

		$this->props[camelCaseString($name)] = $models;
	}

	/**
	 * Adds a taxonomy type
	 * @param string $name
	 * @param Post|null $defaultValue
	 */
	public function addTaxonomy($name, $taxonomy, $defaultValue = null) {
		$val = arrayPath($this->data, $name, $defaultValue);

		$model = null;
		if (!empty($val)) {
			if (is_numeric($val)) {
				$model = Term::term(Context::current(), $val, $taxonomy);
			} else if ($val instanceof \WP_Term) {
				$model = Term::termFromTermData(Context::current(), $val->to_array());
			} else if (is_array($val)) {
				$model = Term::termFromTermData(Context::current(), $val);
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
				$item = $itemCallback($valItem);

				if (!empty($item)) {
					$array[] = $item;
				}
			}
		}

		$this->props[camelCaseString($name)] = $array;
	}

	/**
	 * Adds an item that is transformed with a callback
	 * @param string $name
	 * @param null|callable $itemCallback
	 */
	public function addTransformer($name, $itemCallback) {
		$val = arrayPath($this->data, $name);

		if (!empty($val) && !empty($itemCallback)) {
			$val = $itemCallback($val);
		}

		$this->props[camelCaseString($name)] = $val;
	}

	/**
	 * Adds an item that represents a link (as defined in ACF)
	 * @param $name
	 */
	public function addLink($name) {
		$val = arrayPath($this->data, $name) ?: [];
		$this->props[camelCaseString($name)] = new ACFLink($val);
	}
	//endregion

	//region Magic
	public function __get($name) {
		if (array_key_exists($name, $this->props)) {
			return $this->props[$name];
		}

		trigger_error("Invalid property name '$name'.", E_USER_ERROR);
	}

	public function __isset($name) {
		return isset($this->props[$name]);
	}
	//endregion
}