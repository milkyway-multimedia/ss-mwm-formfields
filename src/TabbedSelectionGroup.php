<?php

class TabbedSelectionGroup extends SelectionGroup {
	public $labelTab;

	public $showAsDropdown = false;

	public function getLabelTab() {
		return $this->labelTab === true ? $this->title : $this->labelTab;
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

	public function FieldList() {
		$list = parent::FieldList();

		$count = 0;
		foreach($list as $item) {
			$item->ID = $this->ID() . '_' . (++$count);
		}

		return $list;
	}

	public function FieldHolder($properties = []) {
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

		$obj = $properties ? $this->customise($properties) : $this;

		return $obj->renderWith($this->getTemplates());
	}

	public function getInitiallySelected() {
		return $this->FieldList()->filter('Selected', true)->exists() ? $this->FieldList()->filter('Selected', true)->first() : null;
	}
}

class TabbedSelectionGroup_Item extends SelectionGroup_Item {

}