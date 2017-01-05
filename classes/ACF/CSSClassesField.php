<?php

namespace ILab\StemContent\ACF;

use ILab\Stem\Core\Context;
use ILab\Stem\Core\Log;

class CSSClassesField extends \acf_field  {
	function __construct() {
		$this->name = 'css_classes';
		$this->label = __('CSS Classes', 'stem-content');
		$this->category = 'choice';
		$this->defaults = [];

		if (Context::current()) {
			$allStyles = Context::current()->ui->setting("content/styles/*", []);
			$otherStyles = Context::current()->ui->setting("content/styles", []);

			$allStylesJSON = [];

			foreach($otherStyles as $type => $styles) {
				if ($type=='*')
					continue;

				$local = [];

				$styles = array_merge($styles, $allStyles);
				foreach($styles as $key => $style) {
					$styleObj = new \stdClass();
					$styleObj->id = $key;
					$styleObj->text = $style;
					$local[] =  $styleObj;
				}

				$allStylesJSON[$type] = $local;
			}

			add_action('acf/input/admin_footer', function() use ($allStylesJSON) {
				?>
				<script>
					var allStylesJSON = <?php echo json_encode($allStylesJSON, JSON_PRETTY_PRINT); ?>;
					(function($){
						acf.add_action('after_duplicate',function($old, $el){
							$($el).find('input[data-type="css-classes"]').each(function(){
								var sel = $(this);
								var contentType = sel.data('content-type');
								sel.select2('destroy');
								console.log(contentType);
								console.log(sel.select2({
									tags: allStylesJSON[contentType],
									width: "100%"
								}));
								sel.select2("container").find("ul.select2-choices").sortable({
									containment: 'parent',
									start: function() { sel.select2("onSortStart"); },
									update: function() { sel.select2("onSortEnd"); }
								});
							});
						});
					})(jQuery);
				</script>
				<?php
			});
		}


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
			'multiple'      => 0,
			'choices'       => $choices
		));
	}

	function update_value($value) {
		if (is_array($value) && (count($value)==1))
			return explode(',',$value[0]);

		return $value;
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
		if ($content_type) {
			$allStyles = Context::current()->ui->setting("content/styles/*", []);
			$styles = Context::current()->ui->setting("content/styles/{$content_type}", []);

			$styles = array_merge($styles, $allStyles);
		}
		Log::info('field',$field);

		$stylesJSON = [];
		foreach($styles as $key => $style) {
			$styleObj = new \stdClass();
			$styleObj->id = $key;
			$styleObj->text = $style;

			$stylesJSON[] = $styleObj;
		}

		$val=$field['value'];
		if (!is_array($val)) {
			if (!empty($val))
				$val = [$val];
			else
				$val = [];
		}

		$suid = 's'.uniqid();

		if (strpos($field['name'],'acfcloneindex')===false) {
			?>
			<input type="hidden" name="<?php echo esc_attr($field['name']) ?>[]" data-type="css-classes" data-content-type="<?php echo $content_type?>" id="<?php echo $suid?>_css" value="<?php echo implode(',',$val) ?>">
			<script>
				(function($){
					var $sel = $('#<?php echo $suid;?>_css');
					$sel.select2({
						tags: <?php echo json_encode($stylesJSON, JSON_PRETTY_PRINT)?>,
						width: "100%"
					});
					$sel.select2("container").find("ul.select2-choices").sortable({
						containment: 'parent',
						start: function() { $sel.select2("onSortStart"); },
						update: function() { $sel.select2("onSortEnd"); }
					});
				})(jQuery);
			</script>
			<?php
		} else {
			?>
			<input type="hidden" name="<?php echo esc_attr($field['name']) ?>[]" data-type="css-classes" data-content-type="<?php echo $content_type?>">
			<?php
		}
	}
}