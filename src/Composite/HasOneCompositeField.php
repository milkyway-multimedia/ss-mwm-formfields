<?php

/**
 * Milkyway Multimedia
 * HasOneCompositeField.php
 *
 * A compositefield that saves the containing fields
 * into a has_one relationship
 *
 * @todo No deletion of object supported...
 *
 * @package milkyway/silverstripe-hasonecompositefield
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */
class HasOneCompositeField extends CompositeField
{
    private static $allowed_actions = [
        'delete',
        'detach',
        //        'add-existing-search',
    ];

    /** @var DataObjectInterface */
    protected $record;

    /** @var array */
    protected $extraData = [];

    /** @var array */
    protected $defaultFromParent = [];

    /** @var array */
    protected $overrideFromParent = [];

    /** @var bool */
    protected $allowEmpty = false;

    /** @var array */
    protected $configuration = [
        'allowDelete'     => false,
        'allowDetach'     => false,
        'allowSelector'   => false,
        'actionsTemplate' => 'HasOneCompositeField_actions',
    ];

    /** @var Callable */
    protected $fieldsCallback;

    /** @var ArrayAccess|array */
    private $originalFields;

    /**
     * @param string $name
     * @param DataObjectInterface $record
     * @param FieldList $fields
     */
    public function __construct($name, $record = null, ArrayList $fields = null)
    {
        $this->name = $name;
        $this->record = $record;

        if (!$fields && $this->record) {
            $fields = $this->fieldsFromRecord($this->record);
        }

        if (!$fields) {
            $fields = FieldList::create();
        }

        parent::__construct($fields);
    }

    /**
     *
     */
    public function detach()
    {
        if ($this->form && $this->form->Record && $this->form->Record->canEdit()) {
            $this->form->Record->{$this->name . 'ID'} = null;
            $this->form->Record->write();
        }
    }

    /**
     *
     */
    public function delete()
    {
        if ((($record = $this->record) || ($record = $this->recordFromForm())) && $record->canDelete()) {
            if ($record->exists()) {
                $record->delete();
            }
            $record->destroy();
        }
    }

    /**
     *
     */
//    public function add_existing_search()
//    {
//        return GridFieldAddExistingSearchHandler::create($this, $this);
//    }

    /**
     * @inheritdoc
     */
    public function setForm($form)
    {
        parent::setForm($form);
        $this->attachFieldsFromFormRecord();
        return $this;
    }

    /**
     * @param DataObjectInterface $record
     * @return HasOneCompositeField
     */
    public function setRecord(DataObjectInterface $record)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * @return DataObjectInterface|null
     */
    public function getRecord()
    {
        return $this->record ? $this->record : $this->recordFromForm();
    }

    /**
     * @param array $data
     * @return HasOneCompositeField
     */
    public function setExtraData($data = [])
    {
        $this->extraData = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param array $data
     * @return HasOneCompositeField
     */
    public function setDefaultFromParent($data = [])
    {
        $this->defaultFromParent = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultFromParent()
    {
        return $this->defaultFromParent;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setOverrideFromParent($data = [])
    {
        $this->overrideFromParent = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getOverrideFromParent()
    {
        return $this->overrideFromParent;
    }

    /**
     * @param Callable $callback
     * @return $this
     */
    public function setFieldsCallback($callback = null)
    {
        $this->fieldsCallback = $callback;
        return $this;
    }

    /**
     * @return Callable
     */
    public function getFieldsCallback()
    {
        return $this->fieldsCallback;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function configure($key, $value = null)
    {
        $this->configuration[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function allowed($key)
    {
        if (in_array(strtolower($key), ['detach', 'delete']) && (!$this->Record || !$this->Record->exists())) {
            return false;
        }

        return isset($this->configuration['allow' . ucfirst($key)]) && $this->configuration['allow' . ucfirst($key)];
    }

    /**
     * @param bool $flag
     * @return HasOneCompositeField
     */
    public function allowEmpty($flag = true)
    {
        $this->allowEmpty = $flag;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasData()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function collateDataFields(&$list, $saveableOnly = false)
    {
        if (!$saveableOnly) {
            $children = $this->children;
            $this->children = $this->FieldList(true);
            parent::collateDataFields($list, $saveableOnly);
            $this->children = $children;
        }
    }

    /**
     * This method takes care of saving all the form data
     * @param DataObjectInterface $parent
     */
    public function saveInto(DataObjectInterface $parent)
    {
        $record = $this->record;

        $parent->flushCache(false); // Flush session cache in case a relation id was changed during form->saveInto

        if (!$record) {
            $relName = substr($this->name, -2) == 'ID' ? substr($this->name, -2, 2) : $this->name;

            if ($parent->hasMethod($relName)) {
                $record = $parent->$relName();
            }
        }

        $fields = $this->FieldList(false);
        $form = $this->formFromFieldList($fields, $this->value);

        if ($record) {
            $formDataFields = $form->Fields()->dataFields();

            foreach ($formDataFields as $dataField) {
                if (($dataField instanceof $this) && $dataField !== $this) {
                    $dataField->saveInto($record);
                }
            }

            if (!$this->allowEmpty && !$record->exists() && !count($this->recursiveArrayFilter($form->Data))) {
                return;
            }

            $form->saveInto($record);
            unset($form);

            $record->flushCache(false);

            // Save extra data into field
            if (count($this->extraData)) {
                $record->castedUpdate($this->extraData);
            }

            if (!$record->exists() && count($this->defaultFromParent)) {
                foreach ($this->defaultFromParent as $pField => $rField) {
                    if (is_numeric($pField)) {
                        if ($record->$rField) {
                            continue;
                        }
                        $record->setCastedField($rField, $parent->$rField);
                    } else {
                        if ($record->$pField) {
                            continue;
                        }
                        $record->setCastedField($rField, $parent->$pField);
                    }
                }
            }

            if (count($this->overrideFromParent)) {
                foreach ($this->overrideFromParent as $pField => $rField) {
                    if (is_numeric($pField)) {
                        $record->setCastedField($rField, $parent->$rField);
                    } else {
                        $record->setCastedField($rField, $parent->$pField);
                    }
                }
            }

            $record->write();

            $fieldName = substr($this->name, -2) == 'ID' ? $this->name : $this->name . 'ID';
            $parent->$fieldName = $record->ID;
        } elseif ($parent) {
            $data = $form->Data;
            unset($form);

            if (count($this->extraData)) {
                $data = array_merge($data, $this->extraData);
            }

            $field = $this->name;
            $parent->$field = $data;
        }
    }

    public function FieldList($prependName = true)
    {
        $fields = parent::FieldList();

        if ((!$fields || !$fields->exists()) && (($record = $this->record) || ($record = $this->recordFromForm()))) {
            $this->children = $fields = $this->fieldsFromRecord($record);
        }

        if ($fields && $fields->exists()) {
            if (!$this->originalFields) {
                $this->originalFields = clone $fields;
            }

            if ($this->value && (is_array($this->value) || ($this->value instanceof DataObjectInterface))) {
                $value = $this->value;
            } else {
                $value = $this->record ? $this->record : $this->recordFromForm();
            }

            if ($value) {
                // HACK: Use a fake Form object to save data into fields
                $this->unprependName($fields);

                $form = $this->formFromFieldList($fields, $value);
                $fields->setForm($this->form);
                unset($form);
            }

            if ($prependName) {
                $this->prependName($fields);
            } elseif (!$value) {
                $this->unprependName($fields);
            }
        }

        return $fields;
    }

    protected function prependName($fields)
    {
        foreach ($fields as $field) {
            if (!$field || $field->PrependedName) {
                continue;
            }

            if ($field->isComposite()) {
                $this->prependName($field->FieldList());
            }

            if (strpos($field->Name, $this->name . '[') !== 0) {
                if (!$field->OriginalName) {
                    $field->OriginalName = $field->Name;
                }

                if (($field instanceof Tab || $field instanceof TabSet) && !$field->OriginalId) {
                    $field->OriginalId = $field->id();
                }

                if ($field->OriginalName != $this->name . '[]' && strpos($this->name . '[' . $field->OriginalName . ']',
                        '[]]') !== -1
                ) {
                    $field->setName($this->name . '[' . $field->OriginalName . ']');

                    // Special setting for tabs since they don't use name for IDs
                    if ($field instanceof Tab || $field instanceof TabSet) {
                        $field->setID($this->name . '-' . $field->OriginalId);
                    }
                }
            }

            $field->PrependedName = true;
            $field->UnPrependedName = false;
        }
    }

    public function Actions()
    {
        return $this->renderWith([$this->configuration['actionsTemplate'], 'HasOneCompositeField_actions']);
    }

    public function getSearchList()
    {
        return isset($this->configuration['dataList']) ? $this->configuration['dataList'] : null;
    }

    public function getList()
    {
        if (($record = $this->record) || ($record = $this->recordFromForm())) {
            return $this->record->get()->filter(['ID' => $record->ID]);
        }

        throw new LogicException('Could not find record to match up to');
    }

    public function setName($name)
    {
        $this->unprependName($this->getChildren());
        $return = parent::setName($name);
        $this->prependName($this->getChildren());

        return $return;
    }

    protected function unprependName(ArrayList $fields)
    {
        foreach ($fields as $field) {
            if (!$field || $field->UnPrependedName) {
                continue;
            }

            if ($field->isComposite()) {
                $this->unprependName($field->FieldList());
            }

            $name = $field->Name;

            if (strpos($name, $this->name . '[') === 0) {
                if ($field->OriginalId) {
                    $field->setID($field->OriginalName);
                }
                if ($field->OriginalName) {
                    $field->setName($field->OriginalName);
                } else {
                    $field->setName(trim(str_replace($this->name . '[', '', $name), ']'));
                }
            }

            $field->PrependedName = false;
            $field->UnPrependedName = true;
        }
    }

    protected function attachFieldsFromFormRecord()
    {
        if (!$this->record && $this->Form) {
            $this->record = $this->recordFromForm();

            if ($this->record && !$this->children) {
                $this->children = $this->fieldsFromRecord($this->record);
            }
        }
    }

    protected function recordFromForm()
    {
        if ($this->Form && $this->Form->Record) {
            $relName = substr($this->name, -2) == 'ID' ? substr($this->name, -2, 2) : $this->name;

            if ($this->Form->Record->hasMethod($relName)) {
                return $this->Form->Record->$relName();
            }
        }

        return null;
    }

    protected function fieldsFromRecord($record)
    {
        if ($record->hasMethod('getHasOneCMSFields')) {
            $fields = $record->getHasOneCMSFields($this->Form ? $this->Form->Record : null);
        } else {
            $fields = $record->getFrontEndFields();
        }

        $fields = $fields ?: \FieldList::create();

        if ($this->fieldsCallback && is_callable($this->fieldsCallback)) {
            call_user_func_array($this->fieldsCallback, [$fields, $record, $this]);
        }

        return $fields;
    }

    protected function formFromFieldList($fields, $value = [])
    {
        $form = new Form(singleton('Controller'), $this->name . '-form', $fields, singleton('FieldList'));
        $form->disableSecurityToken();
        if ($value) {
            $form->loadDataFrom($value);
        }
        return $form;
    }

    protected function recursiveArrayFilter($input, $callback = null)
    {
        foreach ($input as &$value) {
            if (!is_array($value)) {
                continue;
            }
            $value = $this->recursiveArrayFilter($value, $callback);
        }

        return $callback ? array_filter($input, $callback) : array_filter($input);
    }
} 