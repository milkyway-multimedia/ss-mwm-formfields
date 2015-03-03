<?php
/**
 * Milkyway Multimedia
 * AccordionComponentField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class AccordionComponentField extends ComponentFieldHolder {
	protected $subItemType = 'AccordionComponentField_Panel';
	protected $appendType = 'Accordion';

	public function __construct($name, $children = null) {
		parent::__construct($name, $children);
		$this->addExtraClass('panel-group');
	}

	public function Field($properties = array()) {
		$properties['endAccordion'] = true;

		if(!isset($properties['startAccordion']))
			$properties['startAccordion'] = $this->ID();

		if(!isset($properties['accordionParentAttributes']))
			$properties['accordionParentAttributes'] = $this->JSONAttributesHTML;

		if(!isset($properties['accordions']))
			$properties['accordions'] = $this->children;

		return parent::Field($properties);
	}
}

class AccordionComponentField_Panel extends ComponentFieldHolder_Item {
	protected $appendType = 'Panel';

	public function __construct($name, $children = null, $type = 'panel-default') {
		parent::__construct($name, $children);
		$this->addExtraClass('panel ' . $type);
	}

	public function getAttributes() {
		$attrs = array(
			'role' => 'panel',
		);

		return array_merge(parent::getAttributes(), $attrs, $this->attributes);
	}

	public function Field($properties = array()) {
		if(!isset($properties['accordionName']))
			$properties['accordionName'] = $this->ID();

		if(!isset($properties['accordionParent']) && $this->holder)
			$properties['accordionParent'] = $this->holder->ID();

		if(!isset($properties['accordionActive']))
			$properties['accordionActive'] = $this->isActive();

		if(!isset($properties['accordionTitle']) && $this->Title())
			$properties['accordionTitle'] = $this->Title();

		if(!isset($properties['accordionFields']) && $this->children)
			$properties['accordionFields'] = $this->children;

		return parent::Field($properties);
	}
}