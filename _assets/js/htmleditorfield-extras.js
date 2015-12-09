(function ($) {
    $.entwine('ss', function ($) {
        $('textarea.htmleditor').entwine({
            onadd:                  function () {
                var ed = tinyMCE.get(this.attr('id'));
                if (!ed) this._super();
            },
            redraw:                 function () {
                var self = this,
                    id = self.attr('id'),
                    key = id + '--' + window.location.pathname.replace(/\W/g, ''),
                    //_old = ssTinyMceConfig,
                    type = self.data("config"),
                    customConfig = self.data("customConfig") || null,
                    customiseConfig = function(key, val) {
                        if(customConfig === null) {
                            customConfig = {};
                        }

                        customConfig[key] = val;
                    }, buttons;

                //config = $.extend({}, ssTinyMceConfig, config);

                if (self.data('tinymceContentCss')) {
                    customiseConfig('old_content_css', ssTinyMceConfig[type].content_css);
                    customiseConfig('content_css', self.data('tinymceContentCss'));
                }

                if (self.hasClass('limited-with-media') && this.checkIfPluginExistsInConfig('-ssbuttons', ssTinyMceConfig[type]) && !this.checkIfButtonExistsInLimitedConfig('ssmedia', ssTinyMceConfig[type])) {
                    customiseConfig('theme_advanced_buttons1', (customConfig && customConfig.hasOwnProperty('theme_advanced_buttons1') ? customConfig.theme_advanced_buttons1 : ssTinyMceConfig[type].theme_advanced_buttons1) + ',ssmedia,ssflash');
                }

                if (self.hasClass('limited-with-links') && this.checkIfPluginExistsInConfig('-ssbuttons', ssTinyMceConfig[type]) && !this.checkIfButtonExistsInLimitedConfig('sslink', ssTinyMceConfig[type])) {
                    customiseConfig('theme_advanced_buttons1', (customConfig && customConfig.hasOwnProperty('theme_advanced_buttons1') ? customConfig.theme_advanced_buttons1 : ssTinyMceConfig[type].theme_advanced_buttons1) + ',sslink,unlink');
                }

                if (self.hasClass('limited-with-source')) {
                    if(this.checkIfButtonExistsInExtendedConfig('code', ssTinyMceConfig[type]) && !this.checkIfButtonExistsInLimitedConfig('code', ssTinyMceConfig[type])) {
                        customiseConfig('theme_advanced_buttons1', (customConfig && customConfig.hasOwnProperty('theme_advanced_buttons1') ? customConfig.theme_advanced_buttons1 : ssTinyMceConfig[type].theme_advanced_buttons1) + ',code');
                    }

                    if(this.checkIfButtonExistsInExtendedConfig('fullscreen', ssTinyMceConfig[type]) && !this.checkIfButtonExistsInLimitedConfig('fullscreen', ssTinyMceConfig[type])) {
                        customiseConfig('theme_advanced_buttons1', (customConfig && customConfig.hasOwnProperty('theme_advanced_buttons1') ? customConfig.theme_advanced_buttons1 : ssTinyMceConfig[type].theme_advanced_buttons1) + ',fullscreen');
                    }
                }

                if (self.hasClass('limited') && this.checkIfButtonExistsInExtendedConfig('removeformat', ssTinyMceConfig[type]) && !this.checkIfButtonExistsInLimitedConfig('removeformat', ssTinyMceConfig[type])) {
                    customiseConfig('theme_advanced_buttons1', (customConfig && customConfig.hasOwnProperty('theme_advanced_buttons1') ? customConfig.theme_advanced_buttons1 : ssTinyMceConfig[type].theme_advanced_buttons1) + ',removeformat');
                }

                if (self.data('tinymceClasses')) {
                    if (ssTinyMceConfig[type].hasOwnProperty('body_class')) {
                        customiseConfig('body_class', ssTinyMceConfig[type].body_class + ' ' + self.data('tinymceClasses'));
                    }
                    else {
                        customiseConfig('body_class', self.data('tinymceClasses'));
                    }
                }

                var _oldSetup = ssTinyMceConfig[type].hasOwnProperty('setup') && (customConfig && !customConfig.hasOwnProperty('extrasHaveBeenSetup')) ? ssTinyMceConfig[type].setup : null;

                customiseConfig('extrasHaveBeenSetup', true);
                customiseConfig('extrasHaveBeenSetup', function (editor) {
                    var allowed = 0,
                        total = 0,
                        curr = 0,
                        $me = $("#" + editor.editorId),
                        message = '';

                    if ($me.attr("maxlength")) {
                        var max = $me.attr("maxlength"),
                            $indicator = $me.data("tinymceMaxlengthIndicator");

                        if ($indicator) {
                            message = ss.i18n._t('HTMLEditorField.THERE_ARE', 'There are') + ' ' + max + ' ' + ss.i18n._t('HTMLEditorField.CHARACTERS_LEFT', 'characters left');

                            if ($indicator && (typeof $indicator == 'string' || $indicator instanceof String))
                                $indicator = $($indicator);

                            if ($indicator.length) {
                                $indicator.text(message);

                                if (max <= 0)
                                    $indicator.addClass('error');
                                else
                                    $indicator.removeClass('error');
                            }
                        }

                        editor.onKeyUp.add(function (ed, e) {
                            total = self.getTextCountFromEditor(ed);
                            allowed = max - total;

                            if (allowed < 0) allowed = 0;

                            if ($indicator.length) {
                                message = ss.i18n._t('HTMLEditorField.THERE_ARE', 'There are') + ' ' + allowed + ' ' + ss.i18n._t('HTMLEditorField.CHARACTERS_LEFT', 'characters left');

                                if ($indicator.length) {
                                    $indicator.text(message);

                                    if (allowed <= 0)
                                        $indicator.addClass('error');
                                    else
                                        $indicator.removeClass('error');
                                }
                                else {
                                    if (allowed <= 0) {
                                    }
                                    else if (curr != total)
                                        statusMessage(message);
                                }
                            }

                            setTimeout(function () {
                                curr = total;
                            }, 0);
                        });

                        editor.onChange.add(function (ed, e) {
                            total = self.getTextCountFromEditor(ed);
                            allowed = max - total;

                            if (allowed < 0) allowed = 0;

                            if ($indicator.length) {
                                message = ss.i18n._t('HTMLEditorField.THERE_ARE', 'There are') + ' ' + allowed + ' ' + ss.i18n._t('HTMLEditorField.CHARACTERS_LEFT', 'characters left');

                                if ($indicator.length) {
                                    $indicator.text(message);

                                    if (allowed <= 0)
                                        $indicator.addClass('error');
                                    else
                                        $indicator.removeClass('error');
                                }
                                else {
                                    if (allowed <= 0) {
                                    }
                                    else if (curr != total)
                                        statusMessage(message);
                                }
                            }

                            setTimeout(function () {
                                curr = total;
                            }, 0);
                        });

                        editor.onKeyDown.add(function (ed, e) {
                            if (!allowed) {
                                total = self.getTextCountFromEditor(ed);
                                allowed = max - total;
                            }

                            if (allowed <= 0 && e.keyCode != 8 && e.keyCode != 46) {
                                tinymce.dom.Event.cancel(e);
                                errorMessage(ss.i18n.sprintf(
                                    ss.i18n._t('HTMLEditorField.MAX_CHARACTERS', 'This field can only contain %s characters'),
                                    max
                                ));
                            }
                        });
                    }

                    if (_oldSetup)
                        _oldSetup(editor);

                    var selector = ssTinyMceConfig[type].hasOwnProperty('editor_selector') ? ssTinyMceConfig[type].editor_selector : '';

                    editor.onPostRender.add(function (ed) {
                        var $container = $('#' + ed.editorId).siblings('#' + ed.editorContainer);

                        setTimeout(function () {
                            if (ed.container)
                                $(ed.container).addClass(self.attr('class').replace(selector, ''));
                            else if ($container.length)
                                $container.addClass(self.attr('class').replace(selector, ''));
                        }, 10);
                    });
                });

                if(customConfig !== null) {
                    ssTinyMceConfig[key] = $.extend({}, ssTinyMceConfig[type], customConfig);
                    ssTinyMceConfig[key].inheritedFrom = type;
                    self.data("customConfig", ssTinyMceConfig[key]);
                    self.data("config", key);
                    return this._super();
                }
                else {
                    return this._super();
                }
            },
            getTextCountFromEditor: function (editor) {
                if (!editor)
                    editor = this.getEditor();

                var text = editor.getContent().replace(/<[^>]*>/g, '').replace(/\s+/g, ' ');
                text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');

                return text ? text.length : 0;
            },
            checkIfPluginExistsInConfig: function(plugin, config) {
                return config.hasOwnProperty('plugins') && config.plugins.indexOf(plugin) !== -1;
            },
            checkIfButtonExistsInExtendedConfig: function(button, config) {
                return (config.hasOwnProperty('theme_advanced_buttons2') && config.theme_advanced_buttons2.indexOf(button) !== -1) || (config.hasOwnProperty('theme_advanced_buttons3') && config.theme_advanced_buttons3.indexOf(button) !== -1);
            },
            checkIfButtonExistsInLimitedConfig: function(button, config) {
                return (config.hasOwnProperty('theme_advanced_buttons1') && config.theme_advanced_buttons1.indexOf(button) !== -1);
            }
        });

        $('form.htmleditorfield-linkform').entwine({
            getEditorField:     function () {
                return $('#' + this.getEditor().getInstance().editorId);
            },
            googleLinkTracking: function () {
                var $editor = this.getEditorField();
                return $editor.length && $editor.hasClass('google-link-tracking');
            },
            emailFriendly:      function () {
                var $editor = this.getEditorField();
                return $editor.length && $editor.hasClass('email-friendly');
            },
            redraw:             function () {
                this._super();

                var linkType = this.find(':input[name=LinkType]:checked').val(),
                    emailFriendly = this.emailFriendly();

                if (this.googleLinkTracking())
                    this.find('.google-analytics-tracking').show().find('.field').show();

                if (!emailFriendly && (linkType === 'internal' || linkType === 'external') && this.find('.field[id$="TargetModal"]').length) {
                    this.find('.field[id$="TargetModal"]').show();
                }

                if(linkType === 'phone') {
                   this.find('.field#TargetBlank').hide();
                   this.find('.field#phone').show();
                }
            },
            getLinkAttributes:  function (e) {
                var att = this._super(),
                    $utm_source = this.find(':input[name=utm_source]');

                if(this.find(':input[name=LinkType]:checked').val() === 'phone' && att.hasOwnProperty('href'))
                    att.href = 'tel:' + this.find(':input[name=phone]').val();

                if (this.find(':input[name=TargetModal]').is(':checked'))
                    att['data-toggle'] = 'modal';

                if (att.hasOwnProperty('href') && att.href.indexOf('mailto:') !== 0 && att.href.indexOf('tel:') !== 0 && $utm_source.length && $utm_source.val()) {
                    var utm_medium = this.find(':input[name=utm_medium]').val(),
                        utm_term = this.find(':input[name=utm_term]').val(),
                        utm_content = this.find(':input[name=utm_content]').val(),
                        utm_campaign = this.find(':input[name=utm_campaign]').val(),
                        params = {
                            utm_source: encodeURIComponent($utm_source.val())
                        };

                    if (!utm_medium)
                        params.utm_medium = encodeURIComponent('none');
                    else
                        params.utm_medium = utm_medium;

                    if (utm_term)
                        params.utm_term = encodeURIComponent(utm_term);

                    if (utm_content)
                        params.utm_content = encodeURIComponent(utm_content);

                    if (utm_campaign)
                        params.utm_campaign = encodeURIComponent(utm_campaign);

                    if (att.href.indexOf('?') != -1)
                        att.href += '&amp;' + $.param(params).replace('&', '&amp;');
                    else
                        att.href += '?' + $.param(params);
                }

                return att;
            },
            getCurrentLink:     function () {
                var selected = this._super(),
                    gat = this.googleLinkTracking();

                if(selected && selected.hasOwnProperty('LinkType') && selected.LinkType == 'external' && selected.hasOwnProperty('external') && selected.external.match(/^tel:(.*)$/)) {
                    selected.LinkType = 'phone';
                    selected.phone = RegExp.$1;
                    delete selected.external;
                    delete selected.TargetBlank;
                }

                if (selected !== null && !gat)
                    return selected;

                var selectedEl = this.getSelection(),
                    href = '', title = '';

                // We use a separate field for linkDataSource from tinyMCE.linkElement.
                // If we have selected beyond the range of an <a> element, then use use that <a> element to get the link data source,
                // but we don't use it as the destination for the link insertion
                var linkDataSource = null;
                if (selectedEl.length) {
                    if (selectedEl.is('a')) {
                        // Element is a link
                        linkDataSource = selectedEl;
                        // TODO Limit to inline elements, otherwise will also apply to e.g. paragraphs which already contain one or more links
                        // } else if((selectedEl.find('a').length)) {
                        // 	// Element contains a link
                        // 	var firstLinkEl = selectedEl.find('a:first');
                        // 	if(firstLinkEl.length) linkDataSource = firstLinkEl;
                    } else {
                        // Element is a child of a link
                        linkDataSource = selectedEl = selectedEl.parents('a:first');
                    }
                }
                if (linkDataSource && linkDataSource.length) this.modifySelection(function (ed) {
                    ed.selectNode(linkDataSource[0]);
                });

                // Is anchor not a link
                if (!linkDataSource.attr('href')) linkDataSource = null;

                if (linkDataSource) {
                    if (linkDataSource.hasData('toggle') && linkDataSource.data('toggle') == 'modal') {
                        selected.TargetModal = true;
                    }
                }

                if (gat) {
                    if (selected !== null) {
                        var url = linkDataSource.attr('href').replace('&amp;', '&'),
                            query = url.indexOf('?') != -1 ? url.substring(url.indexOf('?') + 1, url.length) : '';

                        if (query) {
                            var utm = this.deparam(query),
                                r;

                            if (utm && !$.isEmptyObject(utm)) {
                                $.each(utm, function (i, e) {
                                    if (i.indexOf('utm_') === 0) {
                                        r = i + '=' + e;
                                        selected[i] = decodeURIComponent(e);
                                        url = url.replace(r, '').replace(/[\?|&]+$/, '');
                                    }
                                });
                            }

                            if (url.match(/^\[sitetree_link(?:\s*|%20|,)?id=([0-9]+)\]?(#.*)?$/i)) {
                                selected.LinkType = 'internal';
                                selected.internal = RegExp.$1;
                                selected.Anchor = RegExp.$2 ? RegExp.$2.substr(1) : '';

                                if (selected.hasOwnProperty('external'))
                                    delete selected.external;
                            }
                            else
                                selected.external = url;
                        }
                    }
                    else {
                        var $editor = this.getEditorField(),
                            utm_source = $editor.data('utmSource'),
                            utm_medium = $editor.data('utmMedium'),
                            utm_term = $editor.data('utmTerm'),
                            utm_content = $editor.data('utmContent'),
                            utm_campaign = $editor.data('utmCampaign');

                        if (utm_source)
                            selected.utm_source = utm_source.indexOf('#') === 0 && $(utm_source).length ? ($(utm_source).is(':input') ? $(utm_source).val() : $(utm_source).text()) : utm_source;

                        if (utm_medium)
                            selected.utm_medium = utm_medium.indexOf('#') === 0 && $(utm_medium).length ? ($(utm_medium).is(':input') ? $(utm_medium).val() : $(utm_medium).text()) : utm_medium;

                        if (utm_term)
                            selected.utm_term = utm_term.indexOf('#') === 0 && $(utm_term).length ? ($(utm_term).is(':input') ? $(utm_term).val() : $(utm_term).text()) : utm_term;

                        if (utm_content)
                            selected.utm_content = utm_content.indexOf('#') === 0 && $(utm_content).length ? ($(utm_content).is(':input') ? $(utm_content).val() : $(utm_content).text()) : utm_content;

                        if (utm_campaign)
                            selected.utm_campaign = utm_campaign.indexOf('#') === 0 && $(utm_campaign).length ? ($(utm_campaign).is(':input') ? $(utm_campaign).val() : $(utm_campaign).text()) : utm_campaign;
                    }
                }

                return selected;
            },
            deparam:            function (query) {
                // remove any preceding url and split
                query = query.substring(query.indexOf('?') + 1).split('&');
                var params = {}, pair, d = decodeURIComponent, i;
                // march and parse
                for (i = query.length; i > 0;) {
                    pair = query[--i].split('=');
                    params[d(pair[0])] = d(pair[1]);
                }

                return params;
            }
        });
    });
})(jQuery);