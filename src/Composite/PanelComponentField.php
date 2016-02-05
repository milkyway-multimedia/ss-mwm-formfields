<?php
/**
 * Milkyway Multimedia
 * AccordionComponentField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class PanelComponentField extends ComponentFieldHolder {
	protected $subItemType = 'AccordionComponentField_Panel';
	protected $appendType = 'Accordion';

	public function __construct($name, $children = null, $type = 'panel-default') {
		parent::__construct($name, $children);
		$this->addExtraClass('panel ' . $type);
	}

	public function Field($properties = array()) {
		if(!isset($properties['panelAttributes']))
			$properties['panelAttributes'] = $this->JSONAttributesHTML;

		if(!isset($properties['panelFields']))
			$properties['panelFields'] = $this->children;

		if(!isset($properties['panelHeading']) && $title = trim($this->Title()))
			$properties['panelHeading'] = $title;

		if(!isset($properties['panelName']))
			$properties['panelName'] = $this->ID();

		return parent::Field($properties);
	}
}