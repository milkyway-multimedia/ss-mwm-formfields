/**
 * Milkyway Multimedia
 *
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

(
    function ($) {
        $.entwine('ss', function($) {
            $('input.typeahead').entwine({
                onmatch: function() {
                    var that = this,
                        engine = that.data('searchEngine');

                    if(that.hasClass('has-typeahead'))
                        return this._super();

                    if(!engine) {
                        var bloodhoundOptions = {
                            datumTokenizer: function(d) {
                                console.log(d);
                                return d;
                            },
                            queryTokenizer: Bloodhound.tokenizers.whitespace,
                            local: that.data('local') ? that.data('local') : null,
                            remote: that.data('remote') ? decodeURIComponent(that.data('remote')) : decodeURIComponent(that.data('suggestUrl'))
                        };

                        if(that.data('prefetch') || that.data('prefetchUrl')) {
                            bloodhoundOptions.prefetch = that.data('prefetch') ? decodeURIComponent(that.data('prefetch')) : decodeURIComponent(that.data('prefetchUrl'));
                        }

                        engine = new Bloodhound(bloodhoundOptions);

                        that.data('searchEngine', engine);
                    }

                    engine.initialize();

                    var data = that.data();

                    if(!data.templates) {
                        data.templates = {};

                        if(data.hasOwnProperty('templates.empty'))
                            data.templates.empty = data['templates.empty'];

                        if(data.hasOwnProperty('templates.footer'))
                            data.templates.footer = data['templates.footer'];

                        if(data.hasOwnProperty('templates.header'))
                            data.templates.header = data['templates.header'];

                        if(data.hasOwnProperty('templates.suggestion'))
                            data.templates.suggestion = data['templates.suggestion'];
                        else {
                            data.templates.suggestion = function(template) {
                                if(template.text)
                                    return '<p>' + template.text + '</p>';
                                else
                                    return '<p>' + template.id + '</p>';
                            };
                        }
                    }

                    that.addClass('has-typeahead').typeahead(null, $.extend({}, data, {
                        source: engine.ttAdapter(),
	                    displayKey: that.data('displayKey') ? that.data('displayKey') : 'id'
                    }));

                    return this._super();
                },
                onunmatch: function() {
                    this._super();
                }
            });
        });
    }(jQuery)
);
