/**
 * Milkyway Multimedia
 *
 *
 * @package
 * @author Mellisa Hankins <mellisa.hankins@me.com>
 */

(
    function ($) {
        $.entwine('ss', function ($) {
            $('select.select2').entwine({
                onmatch:       function () {
                    var $this = this;

                    if ($this.hasClass('has-select2')) {
                        return this._super();
                    }

                    var config = $.extend({}, this.configuration($this), $this.data());

                    $this.addClass('has-select2');

                    if ($this.data('prefetchUrl')) {
                        var results = [];
                        $this[0].disabled = true;

                        $this.addClass('processing');

                        $.ajax({
                            type:     'GET',
                            url:      $this.data('prefetchUrl'),
                            success:  function (response) {
                                results = response;
                            },
                            complete: function () {
                                config.data = {
                                    results: results
                                };

                                $this[0].disabled = false;
                                $this.select2(config);

                                $this.removeClass('processing');
                            }
                        });
                    }
                    else {
                        $this.select2(config);
                    }

                    return this._super();
                },
                onunmatch:     function () {
                    if(this.data('select2')) {
                        this.data('select2').destroy();
                    }

                    this._super();
                },
                configuration: function ($this) {
                    var options = {
                        //width: 'resolve'
                    };

                    if ($this.data('suggestUrl')) {
                        options.ajax = {
                            url:     $this.data('suggestUrl'),
                            cache:   true,
                            data:    function (term) {
                                return {
                                    q: term
                                };
                            },
                            processResults: function (data) {
                                return {
                                    results: data
                                };
                            }
                        };
                    }
                    else if($this.data('local') && $this.data('local').length) {
                        options.data = {
                            results: $this.data('local')
                        };
                    }

                    if ($this.data('allowHtml')) {
                        options.escapeMarkup = function (m) {
                            return m;
                        };
                    }

                    if (!$this.attr('required')) {
                        options.allowClear = true;

                        if (options.hasOwnProperty('data') && !this.hasEmptyItem(options.data.results)) {
                            options.data.results.unshift(options.data.results, {
                                id:   '',
                                text: ($this.attr('placeholder') || $this.data('placeholder') || '')
                            });
                        }
                    }

                    return options;
                },
                hasEmptyItem: function(data) {
                    for(var item in data) {
                        if(!data.hasOwnProperty(item)) {
                            continue;
                        }

                        if(data['item'].hasOwnProperty['id'] && !data['item'].id) {
                            return true;
                        }
                    }

                    return false;
                }
            });
        });
    }(jQuery)
);
