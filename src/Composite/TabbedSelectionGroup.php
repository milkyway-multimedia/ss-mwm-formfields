<?php

class TabbedSelectionGroup extends SelectionGroup {
	public $labelTab;

	public $showAsDropdown = false;

	public $vertical = false;

	public function getLabelTab() {
		return $this->labelTab === true || $this->title ? $this->title : $this->labelTab;
	}

	public function setLabelTab($labelTab) {
		$this->labelTab = $labelTab;
		return $this;
	}

	public function getShowTabsAsDropdown() {
		return $this->showAsDropdown;
	}

	public function showAsDropdown($flag = true) {
		$this->showAsDropdown = $flag;
		return $this;
	}

	public function setShowAsDropdown($flag = true) {
		return $this->showAsDropdown($flag);
	}

	public function getIsVertical() {
		return $this->vertical;
	}

	public function vertical($flag = true) {
		$this->vertical = $flag;
		return $this;
	}

	public function setVertical($flag = true) {
		return $this->vertical($flag);
	}

	public function FieldList() {
		$list = parent::FieldList();

		$count = 0;
		foreach($list as $item) {
			$item->ID = $this->ID() . '_' . (++$count);
		}

		return $list;
	}

	public function FieldHolder($properties = array()) {
		return $this->Field($properties);
	}

	public function Field($properties = []) {
		if (!$this->config()->exclude_js) {
			if(!$this->config()->exclude_js_libraries) {
				Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
				Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/thirdparty/js/tab.js');
			}

			Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/js/tabbedselectiongroup.init.js');
		}

		if (!$this->config()->exclude_css) {
			Requirements::css(SS_MWM_FORMFIELDS_DIR . '/css/tabbedselectiongroup.css');
		}

		return parent::Field($properties);
	}

	public function getInitiallySelected() {
		return $this->FieldList()->filter('Selected', true)->exists() ? $this->FieldList()->filter('Selected', true)->first() : null;
	}
}

class TabbedSelectionGroup_Item extends SelectionGroup_Item {

}