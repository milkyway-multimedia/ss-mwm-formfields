<?php

/**
 * Milkyway Multimedia
 * InlineFormField.php
 *
 * A composite field that saves the data received into another form
 * - An inline form
 *
 * @package dispoze.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */
class InlineFormField extends CompositeField
{
    protected $record;
    protected $inlineForm;
    protected $actionToExecuteOnSaveInto;

    /** @var ArrayAccess|array */
    private $originalFields;

    public function __construct($name, $inlineForm, $actionToExecuteOnSaveInto = null)
    {
        if ($inlineForm instanceof \Milkyway\SS\ZenForms\Contracts\Decorator) {
            $inlineForm = $inlineForm->original();
        }

        $this->name = $name;
        $this->inlineForm = $inlineForm;
        $this->actionToExecuteOnSaveInto = $actionToExecuteOnSaveInto;

        $this->inlineForm->setName($name);
        $this->inlineForm->disableSecurityToken();

        parent::__construct($inlineForm->Fields());
    }

    /**
     * @return \Form|null
     */
    public function getInlineForm()
    {
        $this->IncludeFormTag = false;
        return $this->inlineForm;
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
     * @param \DataObjectInterface $record
     */
    public function saveInto(DataObjectInterface $record)
    {
        if (!$this->actionToExecuteOnSaveInto) {
            return;
        }

        if ($this->actionToExecuteOnSaveInto instanceof FormAction) {
            $this->actionToExecuteOnSaveInto = $this->actionToExecuteOnSaveInto->actionName();
        }

        $funcName = $this->actionToExecuteOnSaveInto;

        if (!$funcName && $defaultAction = $this->inlineForm->defaultAction()) {
            $funcName = $defaultAction->actionName();
        }

        if ($funcName) {
            $this->inlineForm->setButtonClicked($funcName);
        } else {
            if ($this->inlineForm->Record) {
                $this->inlineForm->loadDataFrom($this->value);
                $this->inlineForm->saveInto($this->inlineForm->Record);
                return;
            }
        }

        if (
            // Ensure that the action is actually a button or method on the form,
            // and not just a method on the controller.
            (
                $this->form
                && $this->form->Controller
                && $this->form->Controller->hasMethod($funcName)
                && !$this->form->Controller->checkAccessAction($funcName)
                // If a button exists, allow it on the controller
                && !$this->form->dataFieldByName('action_' . $funcName)
            ) &&
            (
                $this->inlineForm
                && $this->inlineForm->Controller
                && $this->inlineForm->Controller->hasMethod($funcName)
                && !$this->inlineForm->Controller->checkAccessAction($funcName)
                // If a button exists, allow it on the controller
                && !$this->inlineForm->dataFieldByName('action_' . $funcName)
            ) &&
            ($this->form->hasMethod($funcName) && !$this->form->checkAccessAction($funcName)) &&
            ($this->inlineForm->hasMethod($funcName) && !$this->inlineForm->checkAccessAction($funcName))
            // No checks for button existence or $allowed_actions is performed -
            // all form methods are callable (e.g. the legacy "callfieldmethod()")
        ) {
            return;
        }

        $this->inlineForm->loadDataFrom($this->value);
        $request = \Controller::curr()->Request;

        if ($this->inlineForm->Controller->hasMethod($funcName)) {
            $this->inlineForm->Controller->$funcName($this->inlineForm->Data, $this->inlineForm, $request);
            // Otherwise, try a handler method on the form object.
        } elseif ($this->inlineForm->hasMethod($funcName)) {
            $this->inlineForm->$funcName($this->inlineForm->Data, $this->inlineForm, $request);
        } elseif ($field = $this->inlineForm->checkFieldsForAction($this->inlineForm->Fields(), $funcName)) {
            $field->$funcName($this->inlineForm->Data, $this->inlineForm, $request);
        }
    }

    public function FieldList($prependName = true)
    {
        $fields = $this->inlineForm->Fields();

        if ($fields && $fields->exists()) {
            if (!$this->originalFields) {
                $this->originalFields = clone $fields;
            }

            if ($this->value && (is_array($this->value) || ($this->value instanceof DataObjectInterface))) {
                $value = $this->value;
            } else {
                $value = $this->record ? $this->record : null;
            }

            if ($value) {
                $this->unprependName($fields);
                $this->inlineForm->loadDataFrom($this->value);
            }

            if ($prependName) {
                $this->prependName($fields);
            } elseif (!$value) {
                $this->unprependName($fields);
            }
        }

        return $fields;
    }

    public function hasMethod($method)
    {
        if (!$this->inlineForm->Actions()->fieldByName('action_' . $method)) {
            return parent::hasMethod($method);
        }

        return parent::hasMethod($method) || $this->inlineForm->Controller->hasMethod($method) || $this->inlineForm->hasMethod($method) || $this->Form->Controller->hasMethod($method);
    }

    public function checkAccessAction($action)
    {
        return parent::checkAccessAction($action) || $this->inlineForm->Controller->checkAccessAction($action) || $this->Form->Controller->hasMethod($action) || $this->inlineForm->checkAccessAction($action);
    }

    public function __call($method, $arguments)
    {
        $default = $this->actionToExecuteOnSaveInto;

        if ($default instanceof FormAction) {
            $default = $default->actionName();
        }

        if ($method != $default && !$this->inlineForm->Actions()->fieldByName('action_' . $method)) {
            return parent::__call($method, $arguments);
        }

        $arguments[0] = $this->value;
        $this->unprependName($this->inlineForm->Fields());
        $arguments[1] = $this->inlineForm;

        if ($this->inlineForm->Controller->hasMethod($method)) {
            return call_user_func_array([$this->inlineForm->Controller, $method], $arguments);
        }

        if ($this->form->Controller->hasMethod($method)) {
            return call_user_func_array([$this->form->Controller, $method], $arguments);
        }

        return parent::__call($method, $arguments);
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

                if ($field->OriginalName != $this->name . '[]' && strpos($this->name . '[' . $field->OriginalName . ']',
                        '[]]') !== -1
                ) {
                    $field->setName($this->name . '[' . $field->OriginalName . ']');
                }
            }

            $field->PrependedName = true;
            $field->UnPrependedName = false;
        }
    }

    protected function unprependName(FieldList $fields)
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
}
