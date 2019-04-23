<?php

return [
	'priority' => 3,
	'app' => [
		'models' => [
		],
		'taxonomies' => [
		],
		'controllers' => [
			'Content Page' => \Stem\Content\Controllers\ContentPageController::class
		],
		'commands' => [
		],
		'workflows' => [
		]
	],
	'admin' => [
		'pages' => [
		]
	],
	'ui' => [
		'columns' => [
		],
		'fields' => [
			\Stem\Content\Fields\ContentTemplateField::class
		],
		'metaboxes' => [
		],
		'blocks' => [],
		'widgets' => [],
		'directives' => [],
		'shortcodes' => [],
		'enqueue' => [
			'admin' => [
				'css' => [],
				'js' => []
			],
			'public' => [
				'css' => [],
				'js' => []
			]
		]
	]
];