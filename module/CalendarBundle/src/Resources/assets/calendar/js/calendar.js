(function ($) {
    var defaults = {
        url: '',
        month: 0,
        year: 0,
        previousText: 'Previous',
        nextText: 'Next',
        viewText: 'View'
    };

    var methods = {
        init: function (options) {
            var settings = $.extend(defaults, options);

            $(this).data('calendar', settings);
            _init($(this));

            return this;
        },
        next: function () {
            _next($(this));
            return this;
        },
        previous: function () {
            _previous($(this));
            return this;
        }
    };

    $.fn.calendar = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.calendar');
        }
    };

    function _init($this) {
        var month = $this.data('calendar').month;
        var year = $this.data('calendar').year;

        $this.addClass('calendarBrowser')
            .html('').append(
                $('<div>', {'class': 'span3 calendarColumn', 'id': 'calendarColumn1'}),
                $('<div>', {'class': 'span3 calendarColumn', 'id': 'calendarColumn2'}),
                $('<div>', {'class': 'span3 calendarColumn', 'id': 'calendarColumn3'}),
                $('<div>', {'class': 'span3 calendarColumn', 'id': 'calendarColumn4'}),
                $('<ul>', {'class': 'pager'}).append(
                    $('<li>', {'class': 'previous'}).append(
                        $('<a>', {'href': '#'}).html('&larr; ' + $this.data('calendar').previousText).click(function () {$this.calendar('previous')})
                    ),
                    $('<li>', {'class': 'next'}).append(
                        $('<a>', {'href': '#'}).html($this.data('calendar').nextText + ' &rarr;').click(function () {$this.calendar('next')})
                    )
                )
        );

        _loadColumn($this, 1, month + '-' + year);
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        _loadColumn($this, 2, month + '-' + year);
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        _loadColumn($this, 3, month + '-' + year);
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        _loadColumn($this, 4, month + '-' + year);
    }

    function _loadColumn($this, columnNum, param) {
        $('#calendarColumn' + columnNum).html('').spin({
            color: '#ccc',
            width: 4,
            lines: 10
        });
        $.post($this.data('calendar').url + '/' + param, function (data) {
            var column = $('#calendarColumn' + columnNum);
            column.html('')
                .append(
                    $('<h2>').html(data.month)
                );

            $(data.days).each(function () {
                for (key in this) break;
                column.append(
                    day = $('<div>', {'class': 'item'}).html(
                        $('<span>', {'class': 'date calendar'}).html(this[key].date)
                    )
                );
                $(this[key].events).each(function () {
                    day.append(
                        $('<p>').append(
                            $('<i>', {'class': 'icon-time'}),
                            this.startDate, '&mdash;', this.title
                        ),
                        $('<blockquote>').append(
                            $('<small>').html(
                                $('<a>', {'href': this.url}).html($this.data('calendar').viewText)
                            )
                        )
                    );
                });
            });
        }, 'json');
    }

    function _next($this) {
        var month = $this.data('calendar').month;
        var year = $this.data('calendar').year;

        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        $this.data('calendar').month = month;
        $this.data('calendar').year = year;

        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        $('#calendarColumn1').html($('#calendarColumn2').html());
        $('#calendarColumn2').html($('#calendarColumn3').html());
        $('#calendarColumn3').html($('#calendarColumn4').html());

        _loadColumn($this, 4, month + '-' + year);
    }

    function _previous($this) {
        var month = $this.data('calendar').month;
        var year = $this.data('calendar').year;

        month = month - 1 < 1 ? 12 : month - 1;
        year = month == 12 ? year - 1 : year;

        $this.data('calendar').month = month;
        $this.data('calendar').year = year;

        $('#calendarColumn4').html($('#calendarColumn3').html());
        $('#calendarColumn3').html($('#calendarColumn2').html());
        $('#calendarColumn2').html($('#calendarColumn1').html());

        _loadColumn($this, 1, month + '-' + year);
    }
}) (jQuery);