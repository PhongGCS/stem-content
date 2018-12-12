<?php

namespace ILab\StemContent\ACF;

use Stem\Core\Context;
use Stem\Core\Log;

class CSSClassesField extends \acf_field  {
	function __construct() {
		$this->name = 'css_classes';
		$this->label = __('CSS Classes', 'stem-content');
		$this->category = 'choice';
		$this->defaults = [];
		$this->stylesJSON = [];

		if (Context::current()) {
			$allStyles = Context::current()->ui->setting("content/styles/*", []);
			$otherStyles = Context::current()->ui->setting("content/styles", []);

			if (count($this->stylesJSON) == 0) {
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

                    $this->stylesJSON[$type] = $local;
                }
            }

            add_action('acf/input/admin_enqueue_scripts', function() {
                wp_register_style( 'tag-editor-css', ILAB_STEM_CONTENT_URI . 'public/css/jquery.tag-editor.css', false, '1.0.0' );
                wp_enqueue_style( 'tag-editor-css' );


                // register script
                wp_register_script( 'jquery-caret-js', ILAB_STEM_CONTENT_URI . 'public/js/jquery.caret.min.js', false, '1.0.0');
                wp_enqueue_script( 'jquery-caret-js' );

                wp_register_script( 'jquery-tag-editor-js', ILAB_STEM_CONTENT_URI . 'public/js/jquery.tag-editor.min.js', ['jquery-caret-js'], '1.0.0');
                wp_enqueue_script( 'jquery-tag-editor-js' );
            });

			add_action('acf/input/admin_footer', function() {
			    acf_enqueue_scripts()
				?>
				<script>
					(function($){
						acf.add_action('after_duplicate',function($old, $el){
							$($el).find('input[data-type="css-classes"]').each(function(){
                                var sel = $(this);

                                sel.tagEditor({
                                   maxLength: 512,
                                   placeholder: "Select CSS classes ...",
                                   forceLowercase: false,
                                   autocomplete: {
                                       delay: 0,
                                       position: { collision: 'flip' },
                                       source: <?php echo json_encode($this->stylesJSON) ?>
                                   }
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
		if (is_array($value) && (count($value)==1)) {
		    $vals = explode(',',$value[0]);
		    if ((count($vals) > 0) && empty($vals[0])) {
                array_shift($vals);
            }

            return $vals;
        }

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

        $this->stylesJSON = [];
		foreach($styles as $key => $style) {
//            $this->stylesJSON[] = $style;
			$styleObj = new \stdClass();
			$styleObj->value = $key;
			$styleObj->label = $style;

            $this->stylesJSON[] = $styleObj;
		}

		$val=$field['value'];
		if (!is_array($val)) {
			if (!empty($val))
				$val = [$val];
			else
				$val = [];
		}

		$suid = 's'.uniqid();

		?>
        <input type="text" id="<?php echo $suid;?>_css" name="<?php echo esc_attr($field['name']) ?>[]" data-type="css-classes" data-content-type="<?php echo $content_type?>">
        <?php
        if (strpos($field['name'],'acfcloneindex') === false) {
			?>
			<script>
				(function($){
					var $sel = $('#<?php echo $suid;?>_css');

                    $sel.tagEditor({
                        maxLength: 512,
                        initialTags: <?php echo json_encode($val) ?>,
                        placeholder: "Select CSS classes ...",
                        forceLowercase: false,
                        autocomplete: {
                            delay: 0,
                            minLength: 0,
                            position: { collision: 'flip' },
                            source: <?php echo json_encode($this->stylesJSON) ?>
                        }
                    });
				})(jQuery);
			</script>
			<?php
		}
	}
}