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
            minLength: 3,
            display: function () {}
        };

        opts = $.extend(defaults, opts);

        $(document).keydown(function (e) {
            if (70 == e.keyCode && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                if (opts.searchDiv.is(':visible')) {
                    opts.searchDiv.hide();
                    opts.defaultPage.show();
                } else {
                    opts.searchDiv.show();
                    opts.defaultPage.hide();
                    opts.searchString.focus();
                }
            }
        }).keyup(function (e) {
            if (27 == e.keyCode) {
                e.preventDefault();
                opts.searchDiv.hide();
                opts.defaultPage.show();
            }
        });

        opts.searchDiv.append(
            $('<div>', {'class': 'moreResults', 'style': 'text-align:right;display:none;'}).append(
                $('<a>').html('&rarr; ' + opts.allResultsText)
            )
        );

        opts.searchString.data('timeout', null);
        $.each([opts.searchField, opts.searchString], function (i, v) {
            v.bind('change click keyup', function () {
                clearTimeout(opts.searchString.data('timeout'));
                opts.searchString.data('timeout', setTimeout(function () {
                    if ('' == opts.searchString.val()) {
                        opts.clear();
                        opts.searchDiv.find('.moreResults').hide();
                        return;
                    } else if (opts.searchString.val().length < opts.minLength) {
                        return;
                    }
                    $.ajax({
                        url: opts.url + opts.searchField.val() + '/' + opts.searchString.val(),
                        method: 'get',
                        dataType: 'json',
                        success: function (e) {
                            opts.searchDiv.find('.moreResults').toggle(e.length >= 1 && opts.searchPage != '');
                            if (e.length >= 1 && opts.searchPage != '')
                                opts.searchDiv.find('.moreResults a').attr('href', opts.searchPage + opts.searchField.val() + '/' + opts.searchString.val());

                            opts.display(e);
                        }
                    });
                }, 200));
            });
        });
    };
}) (jQuery);