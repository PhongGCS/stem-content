<?php

namespace Stem\Content\Fields;

class PostTypeField extends \acf_field {
	function __construct() {
		$this->name = 'post_type_field';
		$this->label = 'Post Type Field';
		$this->category = 'Relational';
		$this->defaults = [];
		$this->l10n = [];

		parent::__construct();
	}

	function render_field( $field ) {
		$val = $field['value'];

		$postTypes = get_post_types('', 'objects');
		?>
		<select name="<?php echo esc_attr($field['name']) ?>">
			<?php foreach($postTypes as $postType): ?>
				<option value="<?php echo $postType->name ?>"<?php echo ($val == $postType->name) ? 'selected' : ''?>><?php echo $postType->label ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}