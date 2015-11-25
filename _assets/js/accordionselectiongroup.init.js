/**
 * Milkyway Multimedia
 *
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

(function ($) {
    var $document = $(document);

    $document
        .on('click.bs.tab.data-api.ss', '[data-open="tab"]', function (e) {
            if($(e.target).is(':input')) {
                $(e.target).parents('[data-open="tab"]:first').click();
                return;
            }

            var $this = $(this);

            $.fn.tab.call($this, 'show');

            if($this.hasClass('tabbedselectiongroup-option-anchor')) {
                var $selectedTitle = $this.parents('.tabbedselectiongroup-options-holder:first').find('.tabbedselectiongroup-options-selected--title'),
                    $selectedHtml = $this.find('span').html();

                $selectedTitle.html($selectedHtml);
            }
        })
        .on('click.bs.dropdown.data-api.ss', '[data-open-dropdown]', function (e) {
            if($(e.target).is(':input'))
                return;

            $($(this).data('openDropdown')).toggleClass('open');
            $(this).toggleClass('dropdown-open');
        })
        .on('shown.bs.tab', '[data-open="tab"]', function (e) {
            $($(this).data('target')).find(':input:first').focus();
        })
        .on('click.bs.dropdowns::off.data-api.ss',  function (e) {
            if($(e.target).is('[data-open-dropdown]') || $(e.target).parent().is('[data-open-dropdown]'))
                return;

            $('[data-open-dropdown]').each(function() {
                $($(this).data('openDropdown')).removeClass('open');
            });

            $('.dropdown-open').removeClass('dropdown-open');
        });
})(jQuery);
