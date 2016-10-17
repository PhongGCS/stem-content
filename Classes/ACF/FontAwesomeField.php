<?php

namespace ILab\StemContent\ACF;

class FontAwesomeField extends \acf_field  {
	function __construct() {
		$this->name = 'font_awesome';
		$this->label = __('Font Awesome Icon', 'stem-content');
		$this->category = 'choice';
		$this->defaults = [];
		$this->l10n = [ 'error'	=> __('Error! Please enter a higher value', 'acf-FIELD_NAME') ];

		parent::__construct();

		if (is_admin()) {
			wp_enqueue_style('fontawesome',ILAB_STEM_CONTENT_URI.'/bower_components/font-awesome/css/font-awesome.css');
		}
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
		$icons = include ILAB_STEM_CONTENT_DIR . '/data/fa-icons.php';
		ksort($icons);
		$val=$field['value'];
		$suid = 's'.uniqid();
		?>
			<select id="<?php echo $suid; ?>" name="<?php echo esc_attr($field['name']) ?>">
				<option data-icon="none">None</option>
				<?php foreach($icons as $id => $name): ?>
					<option value="<?php echo $id?>" data-icon="<?php echo $id?>" <?php echo (($id==$val) ? 'SELECTED' : '') ?>><?php echo $name?></option>
				<?php endforeach; ?>
			</select>
		<script>
			jQuery('#<?php echo $suid;?>').select2({
				width: "100%",
				formatResult:function(icon) {
					var originalOption = icon.element;
					var iconName = jQuery(originalOption).data('icon');
					if (iconName == 'none')
						return icon.text;
					else
						return '<i style="width:20px;" class="fa ' + iconName + '"></i> ' + icon.text;
				},
				formatSelection:function(icon) {
					var originalOption = icon.element;
					var iconName = jQuery(originalOption).data('icon');
					if (iconName == 'none')
						return icon.text;
					else
						return '<i style="width:20px;" class="fa ' + iconName + '"></i> ' + icon.text;
				}
			});
		</script>
		<?php
	}
}