<?php
/**
 * Milkyway Multimedia
 * FormActionLink.php
 *
 * Sometimes you just need a link in the action bar...
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class FormActionLink extends LiteralField {
	public $cms = false;
	public $addDefaultClasses = true;
	public $modal;
	public $link;

	public function __construct($name, $content, $link) {
		$this->link = $link;
		parent::__construct($name, $content);
	}

	public function cms($flag = true) {
		$this->cms = $flag;
		return $this;
	}

	public function addDefaultClasses($flag = true) {
		$this->addDefaultClasses = $flag;
		return $this;
	}

	public function triggerModal($modal = null) {
		$this->modal = $modal;
		return $this;
	}

	public function getLink() {
		return $this->link;
	}

	public function setLink($link) {
		$this->link = $link;
		return $this;
	}

	public function getAttributes() {
		$attrs = array(
			'class' => $this->extraClass(),
			'id' => $this->ID(),
		);

		if($this->modal) {
			$attrs['data-toggle'] = 'modal';
			$attrs['data-remote'] = 'false';

            $attrs['data-target'] = ($this->modal instanceof \FormField) ? $this->modal->ID() : $this->modal;
            $attrs['data-target'] = '#' . $attrs['data-target'];
		}

		return array_merge($attrs, $this->attributes);
	}

	public function FieldHolder($properties = array()) {
		return $this->Field($properties);
	}

	public function Field($properties = array()) {
		if($this->addDefaultClasses) {
			if($this->cms)
				$this->addExtraClass('action ss-ui-button ui-button');
			else
				$this->addExtraClass('btn');
		}

		$attributes = $this->AttributesHTML;
		$attributes = trim($attributes) ? ' ' . $attributes : '';

		if(is_object($this->content)) {
			$obj = $this->content;

			if($properties)
				$obj = $obj->customise($properties);

			$content = $obj->forTemplate();
		}
		else
			$content = $this->content;

		return sprintf('<a href="%s"%s>%s</a>',
			$this->link,
			$attributes,
			$content
		);
	}
}