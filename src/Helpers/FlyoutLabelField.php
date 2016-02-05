<?php
/**
 * Milkyway Multimedia
 * FlyoutLabelField.php
 *
 * @package rugwash.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class FlyoutLabelField extends \LiteralField {
	public function getAttributes() {
		$attrs = [
			'class' => $this->extraClass(),
			'id' => $this->ID(),
		];

		return array_merge($attrs, $this->attributes);
	}

	public function __construct($name, $content, $label = '') {
		parent::__construct($name, $content);
		$this->title = $label;
	}

	public function FieldHolder($properties = []) {
		return $this->Field($properties);
	}

	public function Field($properties = []) {
		$attributes = $this->AttributesHTML;
		$attributes = trim($attributes) ? ' ' . $attributes : '';

		return sprintf('<div %s><span class="step-label"><span class="flyout">%s</span><span class="arrow"></span><strong class="title">%s</strong></span></div>',
			$attributes,
			$this->Title(),
			$this->Content
		);
	}
} 