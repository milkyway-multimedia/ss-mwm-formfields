<?php

/**
 * Milkyway Multimedia
 * RangeSliderField.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */
class RangeSliderField extends FormField
{
    private static $default_options = [];

    public $inputCallback;

    protected $linkedFields = [];

    protected $settings = [
        'start' => 0,
        'range' => [
            'min' => 0,
            'max' => 100,
        ],
    ];

    protected $inputOnlyAttributes = [
        'type',
        'name',
        'value',
        'required',
        'aria-required',
        'disabled',
        'readonly'
    ];

    public function __construct($name, $title = null, $value = null, $settings = [])
    {
        parent::__construct($name, $title, $value);
        $this->settings = array_merge((array)$this->config()->default_options, $this->settings, $settings);

	    if($this->form)
		    $this->form->addExtraClass('.rangeslider-display--form');
    }

	public function setForm($form) {
		$form->addExtraClass('.rangeslider-display--form');
		return parent::setForm($form);
	}

    function getAttributes()
    {
        $attributes = parent::getAttributes();

        if(isset($attributes['name']))
            $attributes['data-name'] = $attributes['name'];

        array_walk($this->settings, function ($value, $name) use (&$attributes) {
            $attributes['data-' . $name] = trim(json_encode($value, JSON_UNESCAPED_SLASHES), '"\'');
        });

        return array_filter($attributes, function ($name) {
            return !in_array($name, $this->inputOnlyAttributes);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function Inputs()
    {
        if (count($this->linkedFields))
            return \ArrayList::create();

        $ranged = is_array($this->settings['start']) && count($this->settings['start']) > 1;
        $minName = $ranged ? 'min' : 'value';

        $fields[$minName] = [
            'Render' => $this->castedCopy(\NumericField::create($this->Name . '[' . $minName . ']')),
        ];

	    $fields[$minName]['Render']->Value = is_array($this->settings['start']) ? $this->settings['start'][0] : $this->settings['start'];

        if ($ranged) {
            $fields['max'] = [
                'Render' => $this->castedCopy(\NumericField::create($this->Name . '[max]')),
            ];

	        $fields['max']['Render']->Value = $this->settings['start'][1];
        }

        $count = 0;

        array_walk($fields, function ($field) use (&$count) {
            if (!isset($field['Handle']))
                $field['Handle'] = $count % 2 ? 'upper' : 'lower';

            if (isset($field['Render'])) {
                $field['Render']
                    ->removeExtraClass('rangeslider-display')
                    ->addExtraClass('rangeslider-linked')
                    ->setAttribute('data-rangeslider-handle', $field['Handle']);
            }

            $count++;
        });

        $fields = \ArrayList::create(array_map(function ($field) {
            return \ArrayData::create($field);
        }, $fields));

        if($this->inputCallback && is_callable($this->inputCallback))
            call_user_func_array($this->inputCallback, [$fields]);

        $this->extend('updateInputs', $fields);

        return $fields;
    }

    public function set($setting, $value = null)
    {
        if (isset($this->settings[$setting])) {
            $this->settings[$setting] = $value;
            return $this;
        }

        array_set($this->settings, $setting, $value);
        return $this;
    }

    public function get($setting)
    {
        return array_get($this->settings, $setting);
    }

    public function linkTo($field, $linkSettings = [], $id = '')
    {
        if (!$id)
            $id = $field->Name;

        if($linkSettings['handle'])
            $handle = $linkSettings['handle'];
        else
            $handle = (count($this->linkedFields) % 2) ? 'upper' : 'lower';

        $field
            ->addExtraClass('rangeslider-linked')
            ->setAttribute('data-rangeslider-link-to', $this->form ? '#' . $this->ID() : '[name=' . $this->Name . ']')
            ->setAttribute('data-rangeslider-handle', $handle);

        $this->linkedFields[$id] = array_merge($linkSettings, ['formField' => $field]);

        return $this;
    }

    public function unlink($id) {
        if($id instanceof \ViewableData)
            $id = $id->Name;

        if(isset($this->linkedFields[$id])) {
            $this->linkedFields[$id]
                ->removeExtraClass('rangeslider-linked')
                ->removeAttribute('data-rangeslider-link-to')
                ->removeAttribute('data-rangeslider-handle');

            unset($this->linkedFields[$id]);
        }

        return $this;
    }

    public function Field($properties = [])
    {
        if (!$this->config()->exclude_js) {
            Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/thirdparty/js/moment.js');
            Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
            Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
            Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/thirdparty/js/jquery.nouislider.all.js');
            Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/js/rangeslider.init.js');
        }

        if (!$this->config()->exclude_css) {
            Requirements::css(SS_MWM_FORMFIELDS_DIR . '/thirdparty/css/jquery.nouislider.css');
            Requirements::css(SS_MWM_FORMFIELDS_DIR . '/thirdparty/css/jquery.nouislider.pips.css');
            Requirements::css(SS_MWM_FORMFIELDS_DIR . '/css/rangeslider.css');
        }

        $this->addExtraClass('rangeslider-display');

        return parent::Field($properties);
    }
} 