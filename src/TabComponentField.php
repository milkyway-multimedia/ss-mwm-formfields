<?php
/**
 * Milkyway Multimedia
 * TabComponentField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class TabComponentField extends ComponentFieldHolder {
	protected $subItemType = 'TabComponentField_Tab';
	protected $appendType = 'Tabs';

	public function __construct($name, $children = null) {
		parent::__construct($name, $children);
		$this->addExtraClass('tab-content');
	}

	public function Field($properties = array()) {
		if($this->firstActive)
			$this->makeFirstActive();

		$properties['endTabs'] = true;

		if(!isset($properties['startTabs']))
			$properties['startTabs'] = $this->ID();

		if(!isset($properties['tabParentAttributes']))
			$properties['tabParentAttributes'] = $this->JSONAttributesHTML;

		if(!isset($properties['tabs']))
			$properties['tabs'] = $this->children;

		if(!isset($properties['tabNavigation']) && $this->children && $this->children->exists()) {
			$properties['tabNavigation'] = ArrayList::create();

			foreach($this->children as $child) {
				$properties['tabNavigation']->push(ArrayData::create(array(
					'tabActive' => $child->isActive(),
					'tabName' => $child->ID(),
					'tabTitle' => $child->Title(),
				)));
			}
		}

		return parent::Field($properties);
	}
}

class TabComponentField_Tab extends ComponentFieldHolder_Item {
	protected $appendType = 'Tab';

	public function __construct($name, $children = null) {
		$this->addExtraClass('tab-pane fade');
		parent::__construct($name, $children);
	}

	public function getAttributes() {
		$attrs = array(
			'role' => 'content',
		);

		return array_merge(parent::getAttributes(), $attrs, $this->attributes);
	}

	public function FieldHolder($properties = array()) {
		return $this->Field($properties);
	}

	public function Field($properties = array()) {
		if(!isset($properties['tabName']))
			$properties['tabName'] = $this->ID();

		if($this->isActive())
			$this->addExtraClass('active in');

		if(!isset($properties['tabAttributes']))
			$properties['tabAttributes'] = $this->JSONAttributesHTML;

		if(!isset($properties['tabTitle']) && $this->Title())
			$properties['tabTitle'] = $this->Title();

		if(!isset($properties['tabFields']) && $this->children)
			$properties['tabFields'] = $this->children;

		return parent::Field($properties);
	}
}