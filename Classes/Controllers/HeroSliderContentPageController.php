<?php
namespace ILab\StemContent\Controllers;

use ILab\StemContent\Traits\Content\HasHeroSlider;
use ILab\StemContent\Traits\Content\HasHeroSliderInterface;

/**
 * Class HeroSliderContentPageController
 *
 * Controller for pages that use content blocks and a hero slider.
 *
 * @package ILab\StemContent\Controllers
 */
class HeroSliderContentPageController extends ContentPageController  implements HasHeroSliderInterface  {
	use HasHeroSlider;
}