<?php

namespace ILab\StemContent\Models\ShortCodes;

use ILab\Stem\UI\ShortCode;
use ILab\StemContent\Models\Content\CodeBlock;
use ILab\StemContent\Models\ContentBlock;

class CodeBlockShortCode extends ShortCode {
	public function registerUI($shortCode) {
		$ui = [
			'label' => 'Code Block',
		    'listItemImage' => 'dashicons-editor-code',
		    'inner_content' => [
		    	'label' => 'Code',
		        'description' => 'Code to display.'
		    ],
		    'attrs' => [
				[
					'label' => 'Language',
				    'description' => 'The programming language of the code to display',
				    'attr' => 'language',
				    'type' => 'select',
				    'options' => [
				    	['value' => 'php', 'label' => 'PHP'],
					    ['value' => 'css', 'label' => 'CSS'],
					    ['value' => 'javascript', 'label' => 'JavaScript'],
					    ['value' => 'html', 'label' => 'HTML'],
					    ['value' => 'sass', 'label' => 'SASS'],
				    	['value' => 'bash', 'label' => 'Bash Shell']
				    ]
				],
				[
					'label' => 'Display Shell',
					'description' => 'Display as command line output',
					'attr' => 'display-shell',
					'type' => 'checkbox'
				],
				[
					'label' => 'Shell Prompt',
					'description' => 'The shell prompt (only if Display Shell is enabled)',
					'attr' => 'shell-prompt',
					'type' => 'text'
				],
				[
					'label' => 'Display Line Numbers',
					'description' => 'Display line numbers',
					'attr' => 'line-numbers',
					'type' => 'checkbox'
				],
		    ]
		];

		shortcode_ui_register_for_shortcode($shortCode, $ui);
	}

	public function render($attrs = [], $content = null) {
		$data=[
			'code' => $content,
		    'language' => arrayPath($attrs, 'language', 'php'),
		    'display_shell' => arrayPath($attrs, 'display-shell', false),
		    'shell_prompt' => arrayPath($attrs, 'shell-prompt', null),
		    'show_line_numbers' => arrayPath($attrs, 'line-numbers', false),
		    'is_shortcode' => true
		];

		$cb = new CodeBlock($this->context, $data);
		return $cb->render();
	}
}