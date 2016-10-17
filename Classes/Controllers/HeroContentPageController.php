<?php
namespace ILab\StemContent\Controllers;

use ILab\StemContent\Traits\Content\HasHero;
use ILab\StemContent\Traits\Content\HasHeroInterface;

/**
 * Class HeroContentPageController
 *
 * Controller for pages that use content blocks and a hero.
 *
 * @package ILab\StemContent\Controllers
 */
class HeroContentPageController extends ContentPageController  implements HasHeroInterface {
	use HasHero;
}