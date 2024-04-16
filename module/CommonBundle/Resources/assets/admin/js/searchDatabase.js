(function ($) {
    $.searchDatabase = function (opts) {
        var defaults = {
            defaultPage: null,
            searchDiv: null,
            searchPage: '',
            searchString: null,
            searchField: null,
            allResultsText: 'All Results',
            url: '',
            basePage: null,
            minLength: 3,
            display: function () {}
        };

        opts = $.extend(defaults, opts);

        $(document).keydown(function (e) {
            if (70 == e.keyCode && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                if (opts.searchDiv.is(':visible')) {
                    if (opts.searchDiv)
                        opts.searchDiv.hide();
                    opts.defaultPage.show();
                } else {
                    if (opts.searchDiv)
                        opts.searchDiv.show();
                    opts.defaultPage.hide();
                    opts.searchString.focus();
                }
            }
        }).keyup(function (e) {
            if (27 == e.keyCode) {
                e.preventDefault();
                if (opts.searchDiv)
                    opts.searchDiv.hide();
                if (opts.defaultPage)
                    opts.defaultPage.show();
            }
        });

        if (opts.defaultPage) {
            opts.defaultPage.prepend(
                button = $('<div>', {'class': 'search-button'}),
                $('<br>', {'style': 'clear: both'})
            );
            button.click(function () {
                if (opts.searchDiv)
                    opts.searchDiv.show();
                opts.defaultPage.hide();
                opts.searchString.focus();
            });
        }

        if (opts.searchDiv) {
            opts.searchDiv.append(
                $('<div>', {'class': 'moreResults', 'style': 'text-align:right;display:none;'}).append(
                    $('<a>').html('&rarr; ' + opts.allResultsText)
                )
            );
        }

        opts.searchString.after(
            indicator = $('<span>').css({
                'display': 'inline-block',
                'vertical-align': 'middle',
                'position': 'relative',
                'height': opts.searchString.height(),
                'width': '1em',
                'margin-left': '-1.5em',
                'marign-right': '.5em'
            })
        );

        opts.searchString.data('timeout', null);
        $.each([opts.searchField, opts.searchString], function (i, v) {
            v.bind('change click keyup', function () {
                clearTimeout(opts.searchString.data('timeout'));
                opts.searchString.data('timeout', setTimeout(function () {
                    if ('' == opts.searchString.val()) {
                        opts.clear();
                        if (opts.searchDiv)
                            opts.searchDiv.find('.moreResults').hide();
                        return;
                    } else if (opts.searchString.val().length < opts.minLength) {
                        return;
                    }

                    if (opts.searchString.is(':visible')) {
                        indicator.html('');
                        indicator.spin({lines: 9, length: 3, width: 2, radius: 2});
                    }

                    $.ajax({
                        url: opts.url + opts.searchField.val() + '/' + opts.searchString.val(),
                        method: 'get',
                        dataType: 'json',
                        success: function (e) {
                            indicator.spin(false);
                            if (opts.searchDiv) {
                                if (!(opts.searchDiv.is(':visible'))) {
                                    if (opts.searchDiv)
                                        opts.searchDiv.show();
                                    opts.defaultPage.hide();
                                }
                                if (opts.basePage && !(opts.searchPage)) {
                                    opts.searchDiv.find('.moreResults a').attr('href', opts.basePage);
                                }
                                opts.searchDiv.find('.moreResults').toggle(e.length >= 1 && (opts.searchPage || opts.basePage));
                                if (e.length >= 1 && (opts.searchPage || opts.basePage))
                                    if (opts.basePage && !(opts.searchPage)) {
                                        opts.searchDiv.find('.moreResults a').attr('href', opts.basePage);
                                    } else {
                                        opts.searchDiv.find('.moreResults a').attr('href', opts.searchPage + opts.searchField.val() + '/' + opts.searchString.val());
                                    }
                            }
                            opts.display(e);
                        },
                        error: function () {
                            indicator.html(
                                $('<span>').html('!').css({
                                    'color': '#f00',
                                    'font-weight': 'bold'
                                })
                            );
                        }
                    });
                }, 200));
            });
        });
    };
}) (jQuery);
