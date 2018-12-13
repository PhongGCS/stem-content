<?php
namespace ILab\StemContent\Controllers;

use ILab\StemContent\Models\ContentBlock;
use Stem\Controllers\PageController;
use Stem\Core\Context;
use Stem\Core\Response;
use ILab\StemContent\Traits\Content\HasContent;
use ILab\StemContent\Traits\Content\HasContentInterface;
use ILab\StemContent\Traits\Content\HasHeroInterface;
use ILab\StemContent\Traits\Content\HasHeroSliderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContentPageController
 *
 * Controller for pages that use content blocks.
 *
 * @package ILab\StemContent\Controllers
 */
class ContentPageController extends PageController implements HasContentInterface {
	use HasContent;

	protected $targetPagePath;
	protected $defaultViewParameters;

	public function __construct(Context $context, $template=null) {
		if ($template == null)
			$template = 'templates/content-page';

		parent::__construct($context, $template);

		if (!$this->page && $this->targetPagePath) {
			$pagePost = get_page_by_path($this->targetPagePath);

			if ($pagePost)
				$this->page = $context->modelForPost($pagePost);
		}

		if ($this->page) {
			$this->buildContent($context, $this->page);

			if (($this instanceof HasHeroInterface) || ($this instanceof HasHeroSliderInterface))
				$this->buildHero($context, $this->page);
		}
	}

	public function getIndex(Request $request) {
		if ($request->query->has('partial')) {
			$result='';

			/** @var ContentBlock $content */
			foreach($this->content->content as $content) {
				if ($content->supportsPartial($request->query->get('partial'))) {
					$result .= $content->render();
				}
			}

			return new \Symfony\Component\HttpFoundation\Response($result);
		} else {
			$data = [
				'errors' => [],
				'params' => $request->request,
				'content' => $this->content,
				'page' => $this
			];

			if (!empty($this->defaultViewParameters)) {
				$data = array_merge($this->defaultViewParameters, $data);
			}

			if (($this instanceof HasHeroInterface) ||($this instanceof HasHeroSliderInterface)) {
				$data['hero'] = $this->hero();
			}

			$res = new Response($this->template, $data);

			return $res;
		}
	}

}