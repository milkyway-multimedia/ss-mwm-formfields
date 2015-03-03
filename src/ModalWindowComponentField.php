<?php
/**
 * Milkyway Multimedia
 * ModalWindowComponentField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class ModalWindowComponentField extends ComponentFieldHolder {
	/**
	 * @var FieldList
	 */
	protected $footer;

	/**
	 * @var Boolean
	 */
	protected $allowClose = true;

	/**
	 * @var FormField
	 */
	protected $modalTrigger;

	public function __construct($name, $children = null) {
		$this->addExtraClass('modal fade');
		parent::__construct($name, $children);
	}

	public function canClose($flag = true) {
		$this->allowClose = $flag;
		return $this;
	}

	public function NoClose() {
		return !$this->allowClose;
	}

	public function setModalTrigger($button = null) {
		$this->modalTrigger = $button;
		return $this;
	}

	public function getModalTrigger() {
		return $this->modalTrigger;
	}

	public function addTrigger($content = '', $link = '#') {
		if(!$content) {
			if($this->Title())
				$content = _t('ModalWindow_ComponentField.OPEN', 'Open {name}', array('name' => $this->Title()));
			else
				$content = _t('ModalWindow_ComponentField.OPEN', 'Open {name}', array('name' => $this->name_to_label($this->Name)));
		}

		$this->modalTrigger = FormActionLink::create($this->Name. '-Trigger', $content, $link);

		return $this;
	}

	public function getAttributes() {
		$attrs = array(
			'data-modal' => true,
			'tabindex' => '-1',
			'aria-hidden' => true,
		);

		if(!$this->allowClose)
			$attrs['data-backdrop'] = 'static';

		return array_merge(parent::getAttributes(), $attrs, $this->attributes);
	}

	/**
	 * Accessor method for $this->children
	 *
	 * @return FieldList
	 */
	public function getFooter() {
		return $this->footer;
	}

	/**
	 * @param FieldList|FormField|array $children
	 */
	public function setFooter($children) {
		if($children instanceof FieldList)
			$this->footer = $children;
		else
			$this->footer = FieldList::create($children);

		return $this;
	}

	public function Field($properties = array()) {
		if(!isset($properties['ID']))
			$properties['ID'] = $this->ID();

		if(!isset($properties['Title']) && $this->Title())
			$properties['Title'] = $this->Title();

		if(!isset($properties['AttributesHTML']))
			$properties['AttributesHTML'] = $this->AttributesHTML;

		if(!isset($properties['NoClose']) && !$this->allowClose)
			$properties['NoClose'] = true;

		if((!isset($properties['Trigger']) || isset($properties['TriggerButton'])) && $this->modalTrigger) {
			if($this->modalTrigger instanceof FormActionLink)
				$this->modalTrigger->triggerModal($this->ID());

			if($this->modalTrigger instanceof FormField)
				$properties['TriggerButton'] = $this->modalTrigger;
			else
				$properties['Trigger'] = $this->modalTrigger;
		}

		if(!isset($properties['FieldList']) && $this->children)
			$properties['FieldList'] = $this->children;

		if(!isset($properties['FooterFields']) && $this->footer)
			$properties['FooterFields'] = $this->footer;

		return parent::FieldHolder($properties);
	}

	public function FieldHolder($properties = []) {
		return $this->Field($properties);
	}
} 