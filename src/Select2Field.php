<?php

class Select2Field extends TypeAheadField
{

    public $valField = 'ID';

    /**
     * @var boolean $hasEmptyDefault Show the first <option> element as
     * empty (not having a value), with an optional label defined through
     * {@link $emptyString}. By default, the <select> element will be
     * rendered with the first option from {@link $source} selected.
     */
    public $hasEmptyDefault = false;

    /**
     * @var string $emptyString The title shown for an empty default selection,
     * e.g. "Select...".
     */
    public $emptyString = '';

    public $allowHTML = false;

    private $_fetched;

    public function __construct(
        $name,
        $title = null,
        $value = null,
        $sourceList = null,
        $sourceField = null,
        $refField = '',
        $valField = ''
    ) {
        // Swap around to make more compatible with default dropdown
//        if (is_array($value) || ($value instanceof ArrayAccess)) {
//            $currValue = $sourceList;
//            $sourceList = $value;
//            $value = $currValue;
//        }

        parent::__construct($name, $title, $value, $sourceList, $sourceField, $refField, $valField);
    }

    public function setHasEmptyDefault($flag = true)
    {
        $this->hasEmptyDefault = $flag;

        return $this;
    }

    public function getHasEmptyDefault()
    {
        return $this->hasEmptyDefault;
    }

    public function setEmptyString($str)
    {
        $this->setHasEmptyDefault(true);
        $this->emptyString = $str;

        return $this;
    }

    public function getEmptyString()
    {
        return $this->emptyString;
    }

    function getAttributes()
    {
        $this->extraClasses[] = 'has-chzn chzn-done no-chzn';

        $vars = [
            'data-minimum-input-length' => $this->minSearchLength,
            'data-require-selection'    => $this->requireSelection,
            'data-allow-html'           => $this->allowHTML,
        ];

        if ($this->hasEmptyDefault) {
            $placeholder = $this->emptyString ?: _t('Select2DropdownField.SELECT___', 'Select...');
        } else {
            $placeholder = ' ';
        }

        if ($placeholder) {
            $vars['placeholder'] = $placeholder;
            $vars['data-placeholder'] = $placeholder;
        }

        $attrs = array_merge(
            parent::getAttributes(), $vars, [
                'type' => 'hidden',
            ]
        );

        if(isset($attrs['data-prefetch-url'])) {
            unset($attrs['data-prefetch-url']);
        }

        if (isset($attrs['data-suggest-url']) && is_string($attrs['data-suggest-url'])) {
            $attrs['data-suggest-url'] = str_replace(['q=%QUERY', 'q=%25QUERY'], '', $attrs['data-suggest-url']);
        }

        if(isset($attrs['data-suggest-url']) && $list = $this->ListToUse) {
            $count = 0;

            if(is_array($list)) {
                $count = count($list);
            }
            else if($list instanceof SS_List) {
                $count = $list->count();
            }

            if($count && ($prefetch = $this->_fetched ?: json_decode($this->prefetch()->getBody(), true)) && $count == count($prefetch)) {
                unset($attrs['data-suggest-url']);
                unset($attrs['data-minimum-input-length']);
            }
            elseif(!$count) {
                unset($attrs['data-suggest-url']);
            }

            if($count) {
                $this->_fetched = $prefetch;
            }
        }

        if(isset($attrs['multiple']) && $attrs['multiple'] && isset($attrs['name'])) {
            $attrs['name'] .= '[]';
        }

        return $attrs;
    }

    protected function includeJs()
    {
        Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/thirdparty/js/select2.full.js');
        Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/js/select2.init.js');
    }

    protected function includeCss()
    {
        Requirements::css(SS_MWM_FORMFIELDS_DIR . '/thirdparty/css/select2.css');
        Requirements::css(SS_MWM_FORMFIELDS_DIR . '/css/select2.field.css');
    }

    public function Options() {
        $prefetch = $this->_fetched ?: json_decode($this->prefetch()->getBody(), true);
        $this->_fetched = $prefetch;

        if(empty($prefetch)) {
            return ArrayList::create();
        }

        $value = $this->Value();
        if($value instanceof DataList) {
            $value = $value->column($this->valField);
        }

        return ArrayList::create(array_map(function($item) use($value) {
            return ArrayData::create([
                'Value' => isset($item['id']) ? $item['id'] : '',
                'Title' => isset($item['text']) ? $item['text'] : '',
                'Disabled' => isset($item['disabled']) ? $item['disabled'] : false,
                'Locked' => isset($item['locked']) ? $item['locked'] : false,
                'Selected' => is_array($value) ? isset($item['id']) && in_array($item['id'], $value) : isset($item['id']) && $item['id'] == $value,
            ]);
        }, $prefetch));
    }
}