<?php
namespace Stem\Content\Traits;

use Stem\Core\Context;
use Stem\Models\Attachment;
use Stem\Models\Page;
use Stem\Models\Post;

/**
 * Class HasLink
 *
 * Trait for content that has a link clone field attached to it.
 *
 * @package Stem\Content\Traits\Content
 */
trait HasLink {
	protected $linkId = null;
	protected $linkType = null;
	protected $linkIcon = null;
	protected $linkObject = null;
	protected $linkURL = null;
	protected $linkTitle = null;
	protected $linkImage = null;
	protected $linkVideoEmbed = null;
	protected $linkBackgroundImage = null;
	protected $linkCSSClasses = null;
	protected $linkRelationships = null;
	protected $linkOpenInNewWindow = false;
	protected $linkDownloadFileName = null;

	/**
	 * Parses the link by pulling from the ACF field API
	 *
	 * @param $postId
	 * @param Context $context
	 * @param string $prefix
	 */
	public function parseLink($postId, Context $context, $prefix='') {
		$data=[
			"{$prefix}link_type" => get_field("{$prefix}link_type", $postId),
			"{$prefix}link_id" => get_field("{$prefix}link_id", $postId),
			"{$prefix}link_title" => get_field("{$prefix}link_title", $postId),
			"{$prefix}link_image" => get_field("{$prefix}link_image", $postId),
			"{$prefix}link_icon" => get_field("{$prefix}link_icon", $postId),
			"{$prefix}link_url" => get_field("{$prefix}link_url", $postId),
			"{$prefix}link_page_link" => get_field("{$prefix}link_page_link", $postId),
			"{$prefix}link_post_link" => get_field("{$prefix}link_post_link", $postId),
			"{$prefix}link_file_link" => get_field("{$prefix}link_file_link", $postId),
			"{$prefix}link_relative_url" => get_field("{$prefix}link_relative_url", $postId),
			"{$prefix}link_anchor" => get_field("{$prefix}link_anchor", $postId),
			"{$prefix}link_css_class" => get_field("{$prefix}link_css_class", $postId),
			"{$prefix}link_relationship" => get_field("{$prefix}link_relationship", $postId),
			"{$prefix}link_new_window" => get_field("{$prefix}link_new_window", $postId),
			"{$prefix}link_download_file_name" => get_field("{$prefix}link_download_file_name", $postId),
			"{$prefix}link_background_image" => get_field("{$prefix}link_background_image", $postId),
		];

		$this->parseLinkFromData($data, $context, $prefix);
	}

	/**
	 * Parses the link from an array of data returned by the ACF field API
	 *
	 * @param $data
	 * @param Context $context
	 * @param string $prefix
	 */
	public function parseLinkFromData($data, Context $context, $prefix='') {
		$this->linkType = arrayPath($data, "{$prefix}link_type", null);
		if (!$this->linkType || ($this->linkType == 'none')) {
			$this->linkType = 'none';
			return;
		}

		$this->linkId = arrayPath($data, "{$prefix}link_id", null);

		$externalUrl = arrayPath($data, "{$prefix}link_url", null);
		$pageLink = arrayPath($data, "{$prefix}link_page_link", null);
		$postLink = arrayPath($data, "{$prefix}link_post_link", null);
		$fileLinkID = arrayPath($data, "{$prefix}link_file_link", null);
		$relativeUrl = arrayPath($data, "{$prefix}link_relative_url", null);
		$linkAnchor = arrayPath($data, "{$prefix}link_anchor", null);

		$relationships = arrayPath($data, "{$prefix}link_relationship", []);
		if (is_array($relationships) && (count($relationships)>0))
			$this->linkRelationships = implode(' ',$relationships);

		$this->linkOpenInNewWindow = arrayPath($data, "{$prefix}link_new_window", false);
		$this->linkDownloadFileName = arrayPath($data, "{$prefix}link_download_file_name", null);
		$this->linkTitle = arrayPath($data, "{$prefix}link_title", null);
		$this->linkCSSClasses = arrayPath($data, "{$prefix}link_css_class", null);
		$this->linkVideoEmbed = arrayPath($data, "{$prefix}video_embed", null);

		$this->linkIcon = arrayPath($data, "{$prefix}link_icon", null);
		if ($this->linkIcon == 'None') {
			$this->linkIcon = null;
		}

		$linkImageID = arrayPath($data, "{$prefix}link_image", null);
		if ($linkImageID)
			$this->linkImage = $context->modelForPostID($linkImageID);

		$linkBackgroundImageID = arrayPath($data, "{$prefix}link_background_image", null);
		if ($linkBackgroundImageID)
			$this->linkBackgroundImage = $context->modelForPostID($linkBackgroundImageID);

		if (($this->linkType=='post') && $postLink) {
			$this->linkObject = $context->modelForPost($postLink);
			$this->linkURL = $this->linkObject->permalink();
		} else if (($this->linkType=='page') && $pageLink) {
			$this->linkObject = $context->modelForPost($pageLink);
			$this->linkURL = $this->linkObject->permalink();
		} else if (($this->linkType=='file') && $fileLinkID) {
			$this->linkObject = $context->modelForPostID($fileLinkID);
			$this->linkURL = $this->linkObject->url();
		} else if (($this->linkType == 'external') && $externalUrl) {
			$this->linkURL = $externalUrl;
		} else if (($this->linkType == 'relative') && $relativeUrl) {
			$this->linkURL = $relativeUrl;
		} else if ($this->linkType == 'video') {
			$this->linkURL = '#';
			$this->linkCSSClasses .= ' video-popup';
		}

		if (!empty($linkAnchor)) {
			$this->linkURL .= '#'.trim($linkAnchor,'#');
		}
	}

	/**
	 * Returns the link url if any
	 * @return null|string
	 */
	public function linkURL() {
		return $this->linkURL;
	}

	/**
	 * Returns the title of the link
	 * @return null|string
	 */
	public function linkTitle() {
		return $this->linkTitle;
	}

	/**
	 * Link's ID attribute
	 * @return null|string
	 */
	public function linkId() {
		return $this->linkId;
	}

	/**
	 * The link type
	 * @return string
	 */
	public function linkType() {
		return $this->linkType;
	}

	/**
	 * If this links to page, post or file, this is the object it links to.
	 * @return null|Post|Page|Attachment
	 */
	public function linkObject() {
		return $this->linkObject;
	}

	/**
	 * The image in the link.
	 * @return null|Attachment
	 */
	public function linkImage() {
		return $this->linkImage;
	}

	/**
	 * The background image in the link.
	 * @return null|Attachment
	 */
	public function linkBackgroundImage() {
		return $this->linkBackgroundImage;
	}

	/**
	 * Any additional CSS classes
	 * @return null|string
	 */
	public function linkCSSClasses() {
		return $this->linkCSSClasses;
	}

	/**
	 * Values for the rel attribute
	 * @return null|string
	 */
	public function linkRelationships() {
		return $this->linkRelationships;
	}

	/**
	 * Determines if the link should open in a new window or not.
	 * @return bool
	 */
	public function linkOpenInNewWindow() {
		return $this->linkOpenInNewWindow;
	}

	/**
	 * Allows override the file name for file downloads.
	 * @return null|string
	 */
	public function linkDownloadFileName() {
		return $this->linkDownloadFileName;
	}

	/**
	 * The FontAwesome link icon to use, if any
	 * @return null|string
	 */
	public function linkIcon() {
		return $this->linkIcon;
	}

	/**
	 * Renders the link as an html <a> tag.
	 * @param string $imageSize
	 * @param null|array $imageAttrs
	 * @param bool $stripImageDimensions
	 * @param string $linkText
	 * @param string $backgroundImageSize
	 * @param string $textWrap
	 *
	 * @return string The rendered tag.
	 */
	public function renderLinkTag($imageSize='thumbnail', $imageAttrs=null, $stripImageDimensions=false, $linkText = null, $backgroundImageSize=null, $textWrap = false) {
		if (!$this->linkURL || ($this->linkType == 'none'))
			return '';

		$attributes = [
			'href' => $this->linkURL
		];

		if ($this->linkId)
			$attributes['id'] = $this->linkId;

		if ($this->linkCSSClasses)
			$attributes['class'] = $this->linkCSSClasses;

		if ($this->linkRelationships)
			$attributes['rel'] = $this->linkRelationships;

		if ($this->linkOpenInNewWindow)
			$attributes['target'] = '_blank';

		if ($this->linkDownloadFileName)
			$attributes['download'] = $this->linkDownloadFileName;

		$linkContent = [];

		if ($this->linkBackgroundImage) {
			$attributes['style']='background-image:url('.$this->linkBackgroundImage->src($backgroundImageSize).')';
		}

		if ($this->linkImage) {
			$linkContent[] = $this->linkImage->img($imageSize, $imageAttrs, $stripImageDimensions);
		}

		if ($this->linkIcon) {
			$linkContent[] = "<i class='fa {$this->linkIcon}'></i>";
		}

		if ($linkText) {
			$linkContent[] = $linkText;
			if ($this->linkTitle) {
				$attributes['title'] = $this->linkTitle;
			}
		} else if ($this->linkTitle && $this->linkImage) {
			$linkContent[] = "<span>{$this->linkTitle}</span>";
		} else if ($this->linkTitle) {
			if (!empty($textWrap)) {
				$linkContent[] = "<$textWrap>{$this->linkTitle}</$textWrap>";
			} else {
				$linkContent[] = $this->linkTitle;
			}
		}

		$attrs = '';
		foreach($attributes as $key => $value)
			$attrs.="$key='$value' ";

		$attrs = trim($attrs);

		$linkContents = implode('', $linkContent);
		return "<a {$attrs}>{$linkContents}</a>";
	}
}
