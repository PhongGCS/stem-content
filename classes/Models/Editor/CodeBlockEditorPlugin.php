<?php

namespace ILab\StemContent\Models\Editor;

use ILab\Stem\Core\Context;
use ILab\Stem\UI\EditorPlugin;

class CodeBlockEditorPlugin extends EditorPlugin {
	private $languages=[
		'php' => 'PHP',
		'markup' => 'HTML',
		'css' => 'CSS',
		'scss' => 'SASS',
		'bash' => 'Bash',
		'yaml' => 'YAML',
		'json' => 'JSON',
		'twig' => 'Twig',
		'less' => 'Less',
		'sql' => 'SQL',
		'c' => 'C',
		'cpp' => 'C++',
		'csharp' => 'C#',
		'objectivec' => 'Objective-C',
		'swift' => 'Swift',
		'rust' => 'Rust',
		'ruby' => 'Ruby',
		'elixir' => 'Elixir',
		'clike' => 'Other C-like'
	];

	public function __construct(Context $context, array $config) {
		parent::__construct($context, $config);

		if ($this->config && is_array($this->config)) {
			$langs = arrayPath($this->config, 'languages', null);
			if ($langs && is_array($langs))
				$this->languages = $langs;
		}
	}

	public function identifier() {
		return 'code_block';
	}

	public function styles() {
		return ILAB_STEM_CONTENT_URI.'/public/css/code.block.min.css';
	}

	public function scripts() {
		return ILAB_STEM_CONTENT_URI.'public/js/code.block.min.js';
	}

	public function buttons() {
		return ['code_block'];
	}

	public function onBeforeInit($mceSettings) {
		$convertedLangs = [];
		foreach ($this->languages as $val => $text) {
			$convertedLangs[] = ['value' => $val, 'text' => $text];
		}
		?>
		<script>
			var codeBlockLanguages=<?php echo json_encode($convertedLangs, JSON_PRETTY_PRINT); ?>;
		</script>
		<?php
	}
}
