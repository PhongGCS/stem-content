<?php

namespace ILab\StemContent\Models\Content;

use ILab\StemContent\Models\ContentBlock;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

/**
 * Class Text
 *
 * A basic text block
 *
 * @package ILab\StemContent\Models\Content
 */
class CodeBlock extends ContentBlock {
	protected $code = null;
	protected $language = null;
	protected $displayShell = false;
	protected $shellPrompt = null;
	protected $showLineNumbers = false;
	protected $isShortCode = false;

	public function __construct(Context $context, $data = null, Post $post = null, Page $page = null, $template = null) {
		if (!$template)
			$template = (is_admin()) ? 'partials/content/code-block-editor' : 'partials/content/code-block';

		parent::__construct($context, $data, $post, $page, $template);

		$this->code = arrayPath($data, 'code', null);
		$this->language = arrayPath($data, 'language', null);
		$this->displayShell = arrayPath($data, 'display_shell', null);
		$this->shellPrompt = arrayPath($data, 'shell_prompt', null);
		$this->showLineNumbers = arrayPath($data, 'show_line_numbers', null);
		$this->isShortCode = arrayPath($data, 'is_shortcode', false);
	}

	public function code() {
		return $this->code;
	}

	public function isShortCode() {
		return $this->isShortCode;
	}

	public function language() {
		return $this->language;
	}

	public function preCSSClasses() {
		$classes = [];

		if ($this->isShortCode) {
			$class[] = 'shortcode';
		}

		if ($this->displayShell) {
			$classes[] = 'command-line';
		}

		if ($this->showLineNumbers) {
			$classes[] = 'line-numbers';
		}

		return implode(' ', $classes);
	}

	public function preDataAttributes() {
		$attrs = [];
		if ($this->displayShell && $this->shellPrompt) {
			$attrs[] = "data-prompt='{$this->shellPrompt}'";
		}

		return implode(' ', $attrs);
	}
}