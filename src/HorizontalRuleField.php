<?php
/**
 * Milkyway Multimedia
 * FormMessageField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class HorizontalRuleField extends LiteralField {
	public $addDefaultClasses = true;

	public $visible = true;

	public function __construct($name) {
		parent::__construct($name, '');
	}

	public function invisible($flag = false) {
		$this->visible = $flag;
		return $this;
	}

	public function visible($flag = true) {
		$this->visible = $flag;
		return $this;
	}

	public function addDefaultClasses($flag = true) {
		$this->addDefaultClasses = $flag;
		return $this;
	}

	public function getAttributes() {
		$attrs = [
			'class' => $this->extraClass(),
			'id' => $this->ID(),
		];

		return array_merge($attrs, $this->attributes);
	}

	public function FieldHolder($properties = []) {
		return $this->Field($properties);
	}

	public function Field($properties = []) {
		$attributes = $this->AttributesHTML;
		$attributes = trim($attributes) ? ' ' . $attributes : '';

		$tag = $this->visible ? '<hr %s />' : '<p %s><br/></p>';

		return sprintf($tag,
			$attributes
		);
	}
}