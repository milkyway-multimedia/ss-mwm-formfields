<?php
/**
 * Milkyway Multimedia
 * TabComponentField.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class SliderComponentField extends ComponentFieldHolder {
	protected $subItemType = 'SliderComponentField_Slide';
	protected $appendType = 'Slider';

	public function __construct($name, $children = null) {
		parent::__construct($name, $children);
		$this->addExtraClass('carousel slide');
	}

	public function getAttributes() {
		$attrs = array(
			'data-ride' => 'carousel',
			'data-interval' => 'false',
		);

		return array_merge(parent::getAttributes(), $attrs, $this->attributes);
	}

	public function Field($properties = array()) {
		if($this->firstActive)
			$this->makeFirstActive();

		$properties['endCarousel'] = true;
		$properties['outerCarouselNavigation'] = true;
		$properties['carouselNavigationClasses'] = 'carousel-static';

		if(!isset($properties['startCarousel']))
			$properties['startCarousel'] = $this->ID();

		if(!isset($properties['carouselParentAttributes']))
			$properties['carouselParentAttributes'] = $this->JSONAttributesHTML;

		if(!isset($properties['slides']))
			$properties['slides'] = $this->children;

		if(!isset($properties['carouselNavigation']) && $this->children && $this->children->exists()) {
			$properties['carouselName'] = $this->ID();
			$properties['carouselNavigation'] = ArrayList::create();

			foreach($this->children as $child) {
				$properties['carouselNavigation']->push(ArrayData::create(array(
					'carouselTitle' => $child->Title(),
					'carouselActive' => $child->isActive(),
					'carouselName' => $this->ID(),
				)));
			}
		}

		return parent::Field($properties);
	}
}

class SliderComponentField_Slide extends ComponentFieldHolder_Item {
	protected $appendType = 'Slide';

	public function __construct($name, $children = null) {
		$this->addExtraClass('carousel-body item carousel-form');
		parent::__construct($name, $children);
	}

	public function getAttributes() {
		$attrs = array(
			'role' => 'slide',
		);

		return array_merge(parent::getAttributes(), $attrs, $this->attributes);
	}

	public function FieldHolder($properties = array()) {
		return $this->Field($properties);
	}

	public function Field($properties = array()) {
		if(!isset($properties['carouselName']))
			$properties['carouselName'] = $this->ID();

		if($this->isActive())
			$this->addExtraClass('active');

		if(!isset($properties['carouselAttributes']))
			$properties['carouselAttributes'] = $this->JSONAttributesHTML;

		if(!isset($properties['carouselTitle']) && $this->Title())
			$properties['carouselTitle'] = $this->Title();

		if(!isset($properties['carouselFields']) && $this->children)
			$properties['carouselFields'] = $this->children;

		return parent::Field($properties);
	}
}