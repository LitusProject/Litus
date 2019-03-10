(function ($) {
    $.searchDatabase = function (opts) {
        var defaults = {
            allResults: null,
            defaultPage: null,
            minLength: 3,
            searchDiv: null,
            searchField: null,
            searchIndicator: null,
            searchPage: '',
            searchString: null,
            url: '',
            display: function () {}
        };

        opts = $.extend(defaults, opts);

        $(document).keydown(function (e) {
            if (70 == e.keyCode && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();

                if (opts.searchDiv.is(':visible')) {
                    if (opts.searchDiv) {
                        opts.searchDiv.hide();
                    }

                    opts.defaultPage.show();
                } else {
                    if (opts.searchDiv) {
                        opts.searchDiv.show();
                    }

                    opts.defaultPage.hide();
                    opts.searchString.focus();
                }
            }
        }).keyup(function (e) {
            if (27 == e.keyCode) {
                e.preventDefault();

                if (opts.searchDiv) {
                    opts.searchDiv.hide();
                }

                if (opts.defaultPage) {
                    opts.defaultPage.show();
                }
            }
        });

        if (opts.defaultPage) {
            opts.defaultPage.prepend(
                button = $('<div>', {'class': 'search_button'}).append(
                    $('<i>', {'class': 'fas fa-search'})
                ),
                $('<br>', {'style': 'clear: both'})
            );
            button.click(function () {
                if (opts.searchDiv) {
                    opts.searchDiv.show();
                }

                opts.defaultPage.hide();
                opts.searchString.focus();
            });
        }

        if (opts.allResults) {
            opts.allResults.hide();
        }

        if (opts.searchIndicator) {
            opts.searchIndicator.find('.loading').show();
            opts.searchIndicator.find('.error').hide();

            opts.searchIndicator.hide();
        }

        opts.searchString.data('timeout', null);
        $.each([opts.searchField, opts.searchString], function (i, v) {
            v.bind('change click keyup', function () {
                clearTimeout(opts.searchString.data('timeout'));
                opts.searchString.data('timeout', setTimeout(function () {
                    if ('' == opts.searchString.val()) {
                        opts.clear();
                        if (opts.allResults) {
                            opts.allResults.hide();
                        }

                        return;
                    } else if (opts.searchString.val().length < opts.minLength) {
                        return;
                    }

                    if (opts.searchString.is(':visible')) {
                        opts.searchIndicator.find('.loading').show();
                        opts.searchIndicator.find('.error').hide();

                        opts.searchIndicator.show();
                    }

                    $.ajax({
                        url: opts.url + opts.searchField.val() + '/' + opts.searchString.val(),
                        method: 'get',
                        dataType: 'json',
                        success: function (e) {
                            opts.searchIndicator.hide();

                            if (opts.allResults) {
                                opts.allResults.toggle(e.length >= 1 && opts.searchPage != '');

                                if (e.length >= 1 && opts.searchPage != '') {
                                    opts.allResults.attr('href', opts.searchPage + opts.searchField.val() + '/' + opts.searchString.val());
                                }
                            }

                            opts.display(e);
                        },
                        error: function () {
                            opts.searchIndicator.find('.loading').hide();
                            opts.searchIndicator.find('.error').show();

                            opts.searchIndicator.show();
                        }
                    });
                }, 200));
            });
        });
    };
}) (jQuery);
