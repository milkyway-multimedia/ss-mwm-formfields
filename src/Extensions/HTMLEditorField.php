<?php
/**
 * Milkyway Multimedia
 * HTMLEditorField_Toolbar.php
 *
 * @package rugwash.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\FormFields\Extensions;

class HTMLEditorField extends \Extension {
	public function updateLinkForm($form) {
		if($typesField = $form->Fields()->dataFieldByName('LinkType')) {
			$types = $typesField->Source;
			$types['phone'] = _t('HtmlEditorField.PHONE', 'Phone number');
			$typesField->Source = $types;

			$form->Fields()->insertAfter($fields[] = \TextField::create('phone', _t('HtmlEditorField.PHONE', 'Phone number')), 'email');
		}

		$form->Fields()->insertAfter($fields[] = \CompositeField::create(
			\FlyoutLabelField::create('Step-Google-URLTracking', _t('HtmlEditorField.GOOGLE-LINK_TRACKING', 'Google Link Tracking'), '+'),
			$fields[] = \TextField::create('utm_source', _t('HtmlEditorField.GOOGLE-CAMPAIGN_SOURCE', 'Campaign Source'))
				->setDescription(_t('HtmlEditorField.DESC-GOOGLE-CAMPAIGN_SOURCE', 'Referrer/Subject (eg. Google or July Newsletter). This must be filled to track this link.')),
			$fields[] = \TextField::create('utm_medium', _t('HtmlEditorField.GOOGLE-CAMPAIGN_MEDIUM', 'Campaign Medium'))
				->setDescription(_t('HtmlEditorField.DESC-GOOGLE-CAMPAIGN_MEDIUM', 'Marketing Medium (eg. email, banner, CPC). This must be filled to track this link.')),
			$fields[] = \TextField::create('utm_term', _t('HtmlEditorField.GOOGLE-CAMPAIGN_TERM', 'Campaign Term'))
				->setDescription(_t('HtmlEditorField.DESC-GOOGLE-CAMPAIGN_TERM', 'Keywords - usually used only for CPC')),
			$fields[] = \TextField::create('utm_content', _t('HtmlEditorField.GOOGLE-CAMPAIGN_CONTENT', 'Campaign Content'))
				->setDescription(_t('HtmlEditorField.DESC-GOOGLE-CAMPAIGN_CONTENT', 'Used for A/B Testing - to differentiate between links that point to the same URL')),
			$fields[] = \TextField::create('utm_campaign', _t('HtmlEditorField.GOOGLE-CAMPAIGN_NAME', 'Campaign Name'))
				->setDescription(_t('HtmlEditorField.DESC-GOOGLE-CAMPAIGN_NAME', '(eg. promo code, product) Used for keyword analysis - useful for identifying a product promotion or strategic campaign'))
		)->addExtraClass('field google-analytics-tracking')->setTitle('&nbsp;'), 'TargetBlank');

		if($this->owner->config()->bootstrap_modals)
			$form->Fields()->insertAfter($fields[] = \CheckboxField::create('TargetModal', _t('HtmlEditorField.TARGET_MODAL', 'Open link in modal window?')), 'TargetBlank');

		foreach($fields as $field) {
			$field->setForm($form);
		}
	}
}