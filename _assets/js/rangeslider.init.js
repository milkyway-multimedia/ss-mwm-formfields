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
            $('.rangeslider-display').entwine({
                onmatch: function() {
                    var $this = this,
                        config = $this.data(),
                        $inputs = $this.parents('form:first').find('[data-rangeslider-link-to="#' + this.id + '"],[data-rangeslider-link-to="[name=' + $this.data('name') + ']"]').add($this.siblings('.rangeslider-linked'));

                    if($this.hasClass('has-rangeslider'))
                        return this._super();

                    if(config.format) {
                        config.format = $this.setFormat(config.format, config.formatOptions);

                        if(config.format === null)
                            delete config.format;
                    }

                    $this.noUiSlider(config).addClass('has-rangeslider');

                    $inputs.each(function() {
                        var $input = $(this),
                            $link = $this.Link($input.data('rangesliderHandle')),
                            format = null;

                        if($input.data('rangesliderFormat')) {
                            format = $this.setFormat($input.data('rangesliderFormat'), $input.data('rangesliderFormatOptions'));
                        }

                        $link.to($input, null, format);
                    });

                    if(config.pips) {
                        if(config.pips.format) {
                            config.pips.format = $this.setFormat(config.pips.format, config.pips.formatOptions);

                            if(config.pips.format === null)
                                delete config.pips.format;
                        }

                        $this.noUiSlider_pips(config.pips);
                    }

                    return this._super();
                },
                onunmatch: function() {
                    this._super();
                },
                setFormat: function(format, options) {
                    if($.isPlainObject(format))
                        return wbNumb(format);

                    if(!options)
                        options = {};

                    if(format.substring(0, 6) == 'date::' && window.moment) {
                        return {
                            to: function(timestamp) {
                                return moment.unix(Math.round(timestamp)).format(format.substring(6));
                            },
                            from: function(date) {
                                return moment(date, format.substr(6)).unix();
                            }
                        };
                    }

                    return null;
                }
            });
        });
    }(jQuery)
    );
