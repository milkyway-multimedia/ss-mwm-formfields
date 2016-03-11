<?php

class GroupedListboxField extends ListboxField {

	public function Field($properties = array()) {
		if($this->multiple) $this->name .= '[]';

		$options = array();

		$groupedOptions = array();

		if(is_array($this->value)){
			foreach($this->getSource() as $value => $title) {
				if(is_array($title)) {
					$options = array();

					foreach($title as $value2 => $title2) {
						$options[] = new ArrayData(array(
							'Title' => $title2,
							'Value' => $value2,
							'Selected' => (in_array($value2, $this->value) || in_array($value2, $this->defaultItems)),
							'Disabled' => $this->disabled || in_array($value2, $this->disabledItems),
						));
					}

					$groupedOptions[] = new ArrayData(array(
						'Title' => $value,
						'Options' => ArrayList::create($options)
					));
				}
				else {
					$options[] = new ArrayData(array(
						'Title' => $title,
						'Value' => $value,
						'Selected' => (in_array($value, $this->value) || in_array($value, $this->defaultItems)),
						'Disabled' => $this->disabled || in_array($value, $this->disabledItems),
					));
				}
			}
		} else {
			// Listbox was based a singlular value, so treat it like a dropdown.
			foreach($this->getSource() as $value => $title) {
				if(is_array($title)) {
					$options = array();

					foreach($title as $value2 => $title2) {
						$options[] = new ArrayData(array(
							'Title' => $title2,
							'Value' => $value2,
							'Selected' => ($value2 == $this->value || in_array($value2, $this->defaultItems)),
							'Disabled' => $this->disabled || in_array($value2, $this->disabledItems),
						));
					}

					$groupedOptions[] = new ArrayData(array(
						'Title' => $value,
						'Options' => ArrayList::create($options)
					));
				}
				else {
					$options[] = new ArrayData(array(
						'Title' => $title,
						'Value' => $value,
						'Selected' => ($value == $this->value || in_array($value, $this->defaultItems)),
						'Disabled' => $this->disabled || in_array($value, $this->disabledItems),
					));
				}
			}
		}

		if(count($groupedOptions))
			$options = $groupedOptions;

		if($this->emptyString) {
			$options = array(new ArrayData(array(
				'Value' => '',
				'Title' => $this->emptyString,
			))) + $options;
		}

		$properties = array_merge($properties, array(
			'Options' => new ArrayList($options)
		));

		return $this->customise($properties)->renderWith($this->getTemplates());
	}
}
