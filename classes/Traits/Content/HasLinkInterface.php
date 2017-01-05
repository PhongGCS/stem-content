<?php
namespace ILab\StemContent\Traits\Content;

use ILab\Stem\Core\Context;
use ILab\Stem\Models\Attachment;
use ILab\Stem\Models\Page;
use ILab\Stem\Models\Post;

interface HasLinkInterface {
	/**
	 * Parses the link by pulling from the ACF field API
	 *
	 * @param $postId
	 * @param Context $context
	 * @param string $prefix
	 */
	public function parseLink($postId, Context $context, $prefix='');

	/**
	 * Parses the link from an array of data returned by the ACF field API
	 *
	 * @param $data
	 * @param Context $context
	 * @param string $prefix
	 */
	public function parseLinkFromData($data, Context $context, $prefix='');

	/**
	 * Returns the link url if any
	 * @return null|string
	 */
	public function linkURL();

	/**
	 * Returns the title of the link
	 * @return null|string
	 */
	public function linkTitle();

	/**
	 * Link's ID attribute
	 * @return null|string
	 */
	public function linkId();

	/**
	 * The link type
	 * @return string
	 */
	public function linkType();

	/**
	 * If this links to page, post or file, this is the object it links to.
	 * @return null|Post|Page|Attachment
	 */
	public function linkObject();

	/**
	 * The image in the link.
	 * @return null|Attachment
	 */
	public function linkImage();

	/**
	 * Any additional CSS classes
	 * @return null|string
	 */
	public function linkCSSClasses();

	/**
	 * Values for the rel attribute
	 * @return null|string
	 */
	public function linkRelationships();

	/**
	 * Determines if the link should open in a new window or not.
	 * @return bool
	 */
	public function linkOpenInNewWindow();

	/**
	 * Allows override the file name for file downloads.
	 * @return null|string
	 */
	public function linkDownloadFileName();

	/**
	 * Renders the link as an html <a> tag.
	 * @param string $imageSize
	 * @param null|array $imageAttrs
	 * @param bool $stripImageDimensions
	 *
	 * @return string The rendered tag.
	 */
	public function renderLinkTag($imageSize='thumbnail', $imageAttrs=null, $stripImageDimensions=false);
}