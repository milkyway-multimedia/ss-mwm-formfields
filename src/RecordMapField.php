<?php namespace Milkyway\SS\ZenForms\Fields;

/**
 * Milkyway Multimedia
 * RecordMapField.php
 *
 * This maps the form to an additional record, so saving
 * a form withing a form
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

class RecordMapField extends \HiddenField
{
	/** @var array Allowed classes that can be saved into from this field */
	private static $allowed_classes = [
		'Member',
	];

	/** @var array Disable mapping record columns matching this */
	private static $disable_map_fields = [
		'ID',
	];

	/** @var string Unique Identifier for a record saved by this field */
	public $identifier;

	/** @var array A custom field mapping, mapping form field names to record columns */
	public $mapping = [];

	/** @var array Field names to ignore when saving into this field */
	public $ignore = [];

	/** @var array Additional data to save into record */
	public $additionalData = [];

	/** @var array An array of @SS_List to save this record into  */
	public $additionalRelations = [];

	/** @var string The class of the record */
	public $recordClass;

	/** @var \DataObjectInterface The record to save into */
	public $record;

	/** @var array An array of fields/@FormField that is required before any actual saving is done */
	public $requires = [];

	/** @var bool Set whether this field can create new records */
	public $canCreate = true;

	/** @var bool Set whether this field can edit records */
	public $canEdit = true;

	/** @var bool Set whether this field can delete records */
	public $canDelete = false;

	/**
	 * Return an array of fields that can be mapped to a class name
	 * Looks for public_fields set via config, otherwise defaults to summary_fields
	 *
	 * @param string $class
	 * @return array|bool
	 */
	public static function available_map_fields($class)
	{
		if (!in_array($class, self::config()->allowed_classes) || !\ClassInfo::exists($class))
			return false;

		$available = \Config::inst()->get($class, 'public_fields') ? \Config::inst()->get($class, 'public_fields') : array_keys(singleton($class)->summaryFields());

		$fields = array_diff($available, self::config()->disable_map_fields);

		return count($fields) ? $fields : false;
	}

	public function __construct($name, $title = null, $value = null, $mapping = [], $identifier = null)
	{
		if($value && $value instanceof \DataObjectInterface)
			$this->recordClass = get_class($value);
		elseif (\ClassInfo::exists($name) && singleton($name) instanceof \DataObjectInterface)
			$this->recordClass = $name;

		$this->mapping = $mapping;
		$this->identifier = $identifier;

		$this->doNotMapTo();

		parent::__construct($name, $title);
	}

	function doNotMapTo($fields = [])
	{
		$this->ignore = array_merge($this->ignore, $fields, $this->config()->disable_map_fields);

		return $this;
	}

	public function setMapping($map)
	{
		$this->mapping = $map;

		return $this;
	}

	public function addMappedField($field, $recordField)
	{
		$this->mapping[$field] = $recordField;

		return $this;
	}

	public function removeMappedField($field)
	{
		if (isset($this->mapping[$field])) unset($this->mapping[$field]);

		return $this;
	}

	public function setRecordClass($class)
	{
		$this->recordClass = $class;

		return $this;
	}

	public function getRecordClass()
	{
		return $this->recordClass;
	}

	function saveInto(\DataObjectInterface $record = null, $values = [])
	{
		if (!$this->canSaveInto($record)) return null;

		$mapping = (array)$this->mapping;
		$identifier = $this->identifier;

		$id = null;
		$r = null;

		if ($this->record)
			$r = $this->record;
		elseif ($identifier && $this->recordClass) {
			if (($key = array_search($identifier, $mapping)) && $key !== false) {
				if ($this->form && $this->form->fieldByName($identifier))
					$id = $this->form->fieldByName($identifier)->dataValue();
				elseif (count($values) && isset($values[$identifier]))
					$id = $values[$identifier];

				$identifier = $key;
			} elseif ($this->form && $this->form->fieldByName($identifier))
				$id = $this->form->fieldByName($identifier)->dataValue();
			elseif (count($values) && isset($values[$identifier]))
				$id = $values[$identifier];
			elseif ($record)
				$id = $record->$identifier;

			if ($id)
				$r = \DataObject::get($this->recordClass)->filter($identifier, $id)->first();
		}

		if (!$r && $this->canCreate)
			$r = \Object::create($this->recordClass);

		// Do not edit admins through this field
		if (($r instanceof \Member) && \Permission::checkMember($r, 'ADMIN'))
			return null;

		if ($r && $this->canEdit) {
			$data = [];

			if (count($mapping)) {
				foreach ($mapping as $field => $formField) {
					if (!$formField) continue;

					if ($this->form && $this->form->fieldByName($formField))
						$data[$field] = $this->form->fieldByName($formField)->dataValue();
					elseif (count($values) && isset($values[$formField]))
						$data[$field] = $values[$formField];
				}
			} else
				$data = $this->form ? $this->form->Data : $values;

			$data = $data + $this->additionalData;

			foreach ($this->ignore as $field) {
				if (isset($data[$field])) unset($data[$field]);
			}

			foreach ($this->additionalRelations as $relation => $objects) {
				if ($r->hasMethod($relation)) {
					if (!is_array($objects)) $objects = [$objects];
					foreach ($objects as $object) {
						if ($r->$relation() instanceof \SS_List)
							$r->$relation()->add($object);
						else {
							$relationID = $relation . 'ID';
							$r->$relationID = $object->ID;
						}
					}
				}
			}

			if (count($data)) {
				$r->castedUpdate($data)->write();
			}
		}

		return $r;
	}

	function canSaveInto($record = null)
	{
		if ($this->requires) {
			if (!$this->form) return false;

			$requires = (array)$this->requires;

			foreach ($requires as $r => $args) {
				if ($args instanceof \FormField) {
					if (!$args->hasData()) continue;
					if (!$args->dataValue()) return false;
				} elseif ($this->form->Fields()->fieldByName($r)) {
					if (!$this->form->Fields()->fieldByName($r)->hasData()) continue;

					if (is_array($args) && isset($args['value']) && $this->form->Fields()->fieldByName($r)->dataValue() != $args['value'])
						return false;
					elseif ($this->form->Fields()->fieldByName($r)->dataValue() != $args)
						return false;
					elseif (!$this->form->Fields()->fieldByName($r)->dataValue())
						return false;
				} elseif ($this->form->Fields()->fieldByName($args)) {
					if (!$this->form->Fields()->fieldByName($args)->hasData()) continue;
					if (!$this->form->Fields()->fieldByName($args)->dataValue()) return false;
				}
			}
		}

		return true;
	}
}