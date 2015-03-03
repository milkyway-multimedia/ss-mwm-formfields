Milkyway Form Fields
======
**Milkyway Form Fields** is a set of form fields that we use across a variety of projects and didn't think we would need a separate module for each.

## Install
Add the following to your composer.json file
```

    "require"          : {
		"milkyway-multimedia/ss-mwm-formfields": "dev-master"
	}
	
```

## Usage

### Composite Fields
These fields work as composite fields, with the ability to group form fields into certain components

1. HasOneCompositeField: Save a has one relationship as if it is part of the current form. Can also be used to completely save a different record if need be.
2. AccordionComponentField: A composite field that acts like an accordion. Uses Twitter Bootstrap styling.
3. ModalWindowField: A composite field that acts like a modal window, with the option to set a trigger, or to trigger automatically. Uses Twitter Bootstrap styling.
4. PanelComponentField: A composite field that displays as a panel
5. SliderComponentField: A composite field that displays a slider. Uses Twitter Bootstrap styling.
5. TabComponentField: A composite field that displays fields in a tab. Uses Twitter Bootstrap styling.

### Helper Fields
These are fields that use the LiteralField as a base, but are just there to make developing with forms a little bit faster (and more zen)

1. FormActionLink: Display a link like a button - uses Twitter Bootstrap styling - to get it to work in the CMS, make sure you use FormActionLink::create($name, $content, $link)->cms()
2. FormMessageField: Display a message to the user - uses Twitter Bootstrap styling - to get it to work in the CMS, make sure you use FormMessage::create($name, $content, $type)->cms()
3. HorizontalRuleField: Display a horizontal rule, or just separate with blank paragraphs by using SpacerField::create($name)->invisible()
4. IframeField: Display a page in an iframe within the form
5. FlyoutLabelField: Display a label in a flyout number, to better match the Silverstripe CMS styling.

### Functional Fields
1. GroupedListboxField: Allows you to use a two dimensional array with ListboxField
2. RangeSliderField: Display a slider that saves values to the database
3. TabbedSelectionGroup: Display selection groups in tabs, or a tab dropdown (to save space)

### Extended Functionality
1. HTMLEditorField - extras: Adds ability to limit characters on HTMLEditorFields, define more custom configuration such as buttons, iframe CSS and more.

## License 
* MIT

## Version 
* Version 0.1 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")