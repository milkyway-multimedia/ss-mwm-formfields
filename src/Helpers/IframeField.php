<?php
/**
 * Milkyway Multimedia
 * FormMessageField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class IframeField extends FormField {
	private static $allowed_actions = array(
		'preview',
	);

	public function getAttributes() {
		$attrs = array(
			'class' => $this->extraClass(),
			'src' => $this->Link('preview'),
			'frameborder' => '0',
			'id' => $this->ID(),
		);

		return array_merge($attrs, $this->attributes);
	}

	public function Field($properties = array()) {
		$attributes = $this->AttributesHTML;
		$attributes = trim($attributes) ? ' ' . $attributes : '';

		return sprintf('<iframe %s>%s</iframe>',
			$attributes,
			sprintf(_t('IframeField.VIEW_CONTENT_LINK', '<a href="%s" target="_blank">%s</a>'), $this->Link('preview'), $this->Title())
		);
	}

	public function preview() {
		return $this->value;
	}
}