<?php
/**
 * Milkyway Multimedia
 * FormMessageField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class FormMessageField extends LiteralField {
	public $type = 'good';
	public $element = 'div';
	public $component = 'alert';
	public $addDefaultClasses = true;

	public function __construct($name, $content, $type = 'good') {
		$this->type = $type;
		parent::__construct($name, $content);
	}

	public function cms() {
		$this->element ='p';
		$this->component = 'message';
		return $this;
	}

	public function setType($type = 'good') {
		$this->type = $type;
		return $this;
	}

	public function getType() {
		return $this->type;
	}

	public function setElement($element = 'div') {
		$this->element = $element;
		return $this;
	}

	public function getElement() {
		return $this->element;
	}

	public function setComponent($component = 'alert') {
		$this->component = $component;
		return $this;
	}

	public function getComponent() {
		return $this->component;
	}

	public function addDefaultClasses($flag = true) {
		$this->addDefaultClasses = $flag;
		return $this;
	}

	public function getAttributes() {
		$attrs = array(
			'class' => $this->extraClass(),
			'id' => $this->ID(),
		);

		return array_merge($attrs, $this->attributes);
	}

	public function FieldHolder($properties = array()) {
		return $this->Field($properties);
	}

	public function Field($properties = array()) {
		if($this->addDefaultClasses) {
			if($this->cms)
				$this->addExtraClass('message');
			else
				$this->addExtraClass($this->component);
		}

        $this->addExtraClass($this->type);

		$attributes = $this->AttributesHTML;
		$attributes = trim($attributes) ? ' ' . $attributes : '';

		return sprintf('<%s %s>%s</%s>',
			$this->element,
			$attributes,
			$this->content,
			$this->element
		);
	}
}