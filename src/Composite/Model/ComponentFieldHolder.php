<?php

/**
 * Milkyway Multimedia
 * ComponentFieldHolder.php
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

abstract class ComponentFieldHolder extends CompositeField
{
    /**
     * @var mixed
     */
    protected $subItemType;

    /**
     * @var string
     */
    protected $activeItem;

    /**
     * @var boolean
     */
    protected $firstActive = true;

    /**
     * @var string
     */
    protected $appendType;

    /**
     * @var string
     */
    protected $defaultTemplate;

    public function __construct($name, $children = null)
    {
        $this->name = $name;

        if ($children && $type = $this->subItemType) {
            $sub = [];
            foreach ($children as $child) {
                if ($child instanceof $type) {
                    $sub[] = $child;
                }
            }
        } else {
            $sub = $children ? $children : FieldList::create();
        }

        CompositeField::__construct($sub);
    }

    public function setActiveItem($item = null)
    {
        if ($item instanceof FormField) {
            $this->activeItem = $item->Name;
        } else {
            $this->activeItem = $item;
        }

        return $this;
    }

    public function getActiveItem()
    {
        return $this->activeItem;
    }

    public function activeAtStart($flag = false)
    {
        $this->firstActive = $flag;
        return $this;
    }

    public function makeFirstActive($force = false)
    {
        if (((!$this->activeItem && !$force) || $force) && $this->children && $this->children->exists()) {
            $this->activeItem = $this->children->first()->Name;
        }

        return $this;
    }

    public function ID()
    {
        $append = $this->appendType ? $this->appendType : str_replace('ComponentField', '', $this->class);
        if ($append) {
            return $this->form ? $this->form->FormName() . '_' . $this->name . '-' . $append : $this->name . '-' . $append;
        } else {
            return $this->form ? $this->form->FormName() . '_' . $this->name : $this->name;
        }
    }

    public function push(FormField $field)
    {
        parent::push($field);

        if ($field instanceof ComponentFieldHolder_Item) {
            $field->setHolder($this);
        }
    }

    public function insertBefore($field, $insertBefore)
    {
        parent::insertBefore($field, $insertBefore);

        if ($field instanceof ComponentFieldHolder_Item) {
            $field->setHolder($this);
        }

        $this->sequentialSet = null;
    }

    public function insertAfter($field, $insertAfter)
    {
        parent::insertBefore($field, $insertAfter);

        if ($field instanceof ComponentFieldHolder_Item) {
            $field->setHolder($this);
        }

        $this->sequentialSet = null;
    }

    public function getAttributes()
    {
        $attrs = [
            'class' => $this->extraClass(),
            'id'    => $this->ID(),
        ];

        return array_merge($attrs, $this->attributes);
    }

    public function FieldHolder($properties = [])
    {
        return $this->Field($properties);
    }

    public function Field($properties = [])
    {
        if ($this->firstActive) {
            $this->makeFirstActive();
        }

        return parent::Field($properties);
    }

    /**
     * Returns an array of templates to use for rendering {@link FieldH}
     *
     * @return array
     */
    public function getTemplates()
    {
        $template = parent::getTemplates();

        if ($default = $this->defaultTemplate) {
            $key = array_search('FormField', $template);

            if ($key !== false) {
                $default[$key] = $this->defaultTemplate;
            }
        }

        return $template;
    }
}
