<?php

namespace ILab\StemContent\ACF;

use Stem\Core\Context;
use Stem\Core\Log;

class ContentTemplateField extends \acf_field  {
	function __construct() {
		$this->name = 'content_template';
		$this->label = __('Content Template', 'stem-content');
		$this->category = 'choice';
		$this->defaults = [];

		parent::__construct();
	}

	/**
	 *  render_field_settings()
	 *
	 *  Create extra settings for your field. These are visible when editing a field
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field (array) the $field being edited
	 *  @return	n/a
	 */
	function render_field_settings( $field ) {
		$templateKeys=array_keys(Context::current()->ui->setting('content/templates',[]));

		$choices=[];
		foreach($templateKeys as $key)
			$choices[$key] = $key;


		acf_render_field_setting( $field, array(
			'label'         => __('Content Type','stem-content'),
			'instructions'  => __('Select the content type to display alternate templates for.','stem-content'),
			'type'          => 'select',
			'name'          => 'content_type',
			'layout'        => 'horizontal',
			'multiple'      => 0,
			'choices'       => $choices
		));
	}

	/**
	 *  render_field()
	 *
	 *  Create the HTML interface for your field
	 *
	 *  @param	$field (array) the $field being rendered
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$field (array) the $field being edited
	 *  @return	n/a
	 */
	function render_field( $field ) {
		$content_type=arrayPath($field, 'content_type', null);

		$templates = [];
		if ($content_type) {
			$allTemplates = Context::current()->ui->setting("content/templates/*", []);
			$templates = Context::current()->ui->setting("content/templates/{$content_type}", []);

			$templates = array_merge($templates, $allTemplates);
		}

		$val=$field['value'];

		?>
		<select name="<?php echo esc_attr($field['name']) ?>">
			<option value="">None</option>
			<?php foreach($templates as $id => $name): ?>
				<option value="<?php echo $id?>" <?php echo (($id==$val) ? 'SELECTED' : '') ?>><?php echo $name?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}