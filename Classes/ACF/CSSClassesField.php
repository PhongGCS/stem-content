<?php

namespace ILab\StemContent\ACF;

use ILab\Stem\Core\Context;

class CSSClassesField extends \acf_field  {
	function __construct() {
		$this->name = 'css_classes';
		$this->label = __('CSS Classes', 'stem-content');
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
		$styleKeys=array_keys(Context::current()->ui->setting('content/styles',[]));

		$choices=[];
		foreach($styleKeys as $key)
			$choices[$key] = $key;


		acf_render_field_setting( $field, array(
			'label'         => __('Content Type','stem-content'),
			'instructions'  => __('Select the content type to display CSS classes for.','stem-content'),
			'type'          => 'select',
			'name'          => 'content_type',
			'layout'        => 'horizontal',
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

		$styles = [];
		if ($content_type)
			$styles = Context::current()->ui->setting("content/styles/{$content_type}", []);

		$val=$field['value'];
		$suid = 's'.uniqid();
		?>
		<select id="<?php echo $suid; ?>" name="<?php echo esc_attr($field['name']) ?>">
			<option data-icon="none">None</option>
			<?php foreach($styles as $id => $name): ?>
				<option value="<?php echo $id?>" data-icon="<?php echo $id?>" <?php echo (($id==$val) ? 'SELECTED' : '') ?>><?php echo $name?></option>
			<?php endforeach; ?>
		</select>
		<script>
			jQuery('#<?php echo $suid;?>').select2({
				width: "100%"
			});
		</script>
		<?php
	}
}