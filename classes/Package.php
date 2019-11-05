<?php
namespace Stem\Content;

use Stem\Content\Models\ContentBlock;
use Stem\Core\Context;
use StoutLogic\AcfBuilder\FieldsBuilder;
use StoutLogic\AcfBuilder\LocationBuilder;

class Package extends \Stem\Packages\Package {
	public function __construct($rootPath, $title = null, $description = null) {
		parent::__construct($rootPath, $title, $description);

		add_action('heavymetal/app/models/complete', function(Context $context) {
			if ($context->ui->setting('content/pageContent/enabled', false)) {
				$this->processContentBlocks($context);
			}
		});
	}

	private function processContentBlocks(Context $context) {
		$blockClasses = apply_filters('heavymetal/ui/content/blocks', []);
		$blockClasses = array_merge($blockClasses, $context->ui->setting('content/blocks', []));

		$fieldsCache = [];

		/** @var ContentBlock $blockClass */
		foreach($blockClasses as $blockClass) {
			if(!class_exists($blockClass) || !is_subclass_of($blockClass, ContentBlock::class)) {
				continue;
			}

			$id = $blockClass::identifier();
			if(empty($id)) {
				throw new \Exception("Block class '$blockClass' has a null identifier.");
			}

			$blockTitle = $blockClass::title();
			if(empty($blockTitle)) {
				throw new \Exception("Block class '$blockClass' has a null title.");
			}

			$fields = $blockClass::buildFields();
			$fieldsCache[$id] = $fields;

			if (!empty($fields)) {
				$blockClass::updatePropertyTypes($fields->build());
			}
		}

		$targets = apply_filters('heavymetal/ui/content/pageContent/targets', []);
		$targets = array_merge($targets, $context->ui->setting('content/pageContent/targets', []));

		$index = 0;
		foreach($targets as $targetName => $target) {
			/** @var FieldsBuilder $targetBuilder */
			$targetBuilder = new FieldsBuilder($targetName, ['position' => 'acf_after_title', 'menu_order' => $index]);
			$targetBuilder->setGroupConfig('hide_on_screen', [
				"the_content",
				"discussion",
				"comments",
				"format",
				"send-trackbacks"
			]);

			$flexible = $targetBuilder->addFlexibleContent($targetName, ['title' => $target['title'], 'button_label' => 'Add Content']);

			/** @var ContentBlock $blockClass */
			foreach($blockClasses as $blockClass) {
				if (!class_exists($blockClass) || !is_subclass_of($blockClass, ContentBlock::class)) {
					continue;
				}

				$id = $blockClass::identifier();
				$blockTitle = $blockClass::title();

				$fields = $fieldsCache[$id];

				$viableTargets = $blockClass::contentTargets();
				if (!in_array($targetName, $viableTargets)) {
					continue;
				}

				$layout =  $flexible->addLayout($id, ['title' => $blockTitle, 'display' => 'block']);
				if (!empty($fields) && is_a($fields, FieldsBuilder::class)) {
					$layout->addFields($fields);
				}
			}

			$flexible->endFlexibleContent();

			/** @var LocationBuilder $location */
			$location = null;
			foreach($target['pages'] as $page) {
				$pageTemplate = str_replace(' ', '-', $page).'.php';

				if ($location == null) {
					$location = $targetBuilder->setLocation('page_template', '==', $pageTemplate);
				} else {
					$location->or('page_template', '==', $pageTemplate);
				}
			}

			if (function_exists('acf_add_local_field_group')) {
				$targetFields = $targetBuilder->build();
				acf_add_local_field_group($targetFields);
			}

			$index++;
		}
	}
}