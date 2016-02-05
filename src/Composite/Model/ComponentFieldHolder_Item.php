<?php

/**
 * Milkyway Multimedia
 * ComponentFieldHolder_Item.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

abstract class ComponentFieldHolder_Item extends CompositeField {
	/**
	 * @var ComponentFieldHolder
	 */
	protected $holder;

	/**
	 * @var string
	 */
	protected $appendType;

	public function __construct($name, $children = null) {
		$this->setName($name);
		if(!$children) $children = array();
		parent::__construct($children);
	}

	public function setHolder($holder) {
		$this->holder = $holder;
		return $this;
	}

	public function isActive() {
		if($this->holder)
			return $this->name == $this->holder->ActiveItem;

		return true;
	}

	public function setActive() {
		if($this->holder)
			$this->holder->setActiveItem($this);

		return $this;
	}

	public function ID() {
		$id = $this->appendType ? $this->name . '-' . $this->appendType : $this->name;

		if($this->holder)
			return $this->holder->ID() . '_' . $id;

		return $this->form ? $this->form->FormName() . '_' . $id : $id;
	}

	public function getAttributes() {
		$disabled = $this->isDisabled();

		if($disabled) {
			if($this->appendType)
				$this->addExtraClass(strtolower($this->appendType) . '-disabled disabled');
			else
				$this->addExtraClass('disabled');
		}

		$attrs = array(
			'class' => $this->extraClass(),
			'id' => $this->ID(),
			'data-disabled' => $disabled,
		);

		return array_merge($attrs, $this->attributes);
	}

	public function FieldHolder($properties = array()) {
		return $this->Field($properties);
	}
}
