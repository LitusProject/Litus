(function ($) {
    var defaults = {
        url: '',
        month: 0,
        year: 0,
        previousText: 'Previous',
        nextText: 'Next'
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
                $('<div>', {'class': 'row calendarRow', 'style': 'margin-left: 0px; margin-right: 0px', 'id': 'calendarRow1'}),
                $('<div>', {'class': 'row calendarRow', 'style': 'margin-left: 0px; margin-right: 0px', 'id': 'calendarRow2'}),
                $('<div>', {'class': 'row calendarRow', 'style': 'margin-left: 0px; margin-right: 0px', 'id': 'calendarRow3'}),
                $('<div>', {'class': 'row calendarRow', 'style': 'margin-left: 0px; margin-right: 0px', 'id': 'calendarRow4'}),
                $('<div>', {'class': 'col-md-12 hr clearFix'}),
                $('<ul>', {'class': 'col-md-12 pager'}).append(
                    $('<li>', {'class': 'previous'}).append(
                        $('<a class="calendarButton">', {'href': '#'}).html('&larr; ' + $this.data('calendar').previousText).click(function () {$this.calendar('previous'); return false;})
                    ),
                    $('<li>', {'class': 'next'}).append(
                        $('<a>', {'class': 'calendarButton', 'href': '#'}).html($this.data('calendar').nextText + ' &rarr;').click(function () {$this.calendar('next'); return false;})
                    )
                )
        );

        _loadRow($this, 1, month + '-' + year);
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        _loadRow($this, 2, month + '-' + year);
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        _loadRow($this, 3, month + '-' + year);
        month = month + 1 > 12 ? 1 : month + 1;
        year = month == 1 ? year + 1 : year;

        _loadRow($this, 4, month + '-' + year);
    }

    function _loadRow($this, columnNum, param) {
        $('#calendarRow' + columnNum).html('').spin({
            color: '#abcabc',
            length: 0,
            width: 4,
            lines: 10, 
            position: "relative", 
        }).attr('month', param);
        $.post($this.data('calendar').url + param, function (data) {
            var column = $this.find('.calendarRow[month="' + param + '"]');

            var nestedEvents = []; 
            $.each(data.days, function (key, value) {
                nestedEvents.push(value.events); 
            })

            if (nestedEvents.length > 0) {

                column.html('')
                .append(
                    $('<div>', {'style': 'height: 30px; width: 1px'}),
                    $('<h3>', {'style': 'width: 100%'}).html(data.month)
                );

                $.each(data.days, function (key, value) {
                    $(value.events).each(function () {
                        column.append(
                        $('<div>', {'class': 'col-md-4 calendarItemHolder', 'style': 'border: 2px solid white'}).append(
                            $('<h4>',
                                {
                                    'class': 'calendarTitleStretch'
                                }
                            ).append(
                                this.title + " | " + value.weekday + " " + value.day + " " + value.month
                            ),
                            // $('<div>', {'class': 'button', 'style': 'margin-bottom: 20px; margin-top: 20px'}).append($('<a>', {'href': this.url}).append('Lees meer')),
                            $('<div>', {'class': 'calendarImagePlaceHolder', 'style': '  background-image: url(\'https://vtk.be' + this.poster + '\');'}),
                            $('<p>', {'style': 'min-height: 63px'}).append(this.summary),
                            $('<a>', {'class': 'button blue unfilled', 'href': this.url}).append('Lees meer')
                        )
                    );

                    // NOTE TO SELF: give calendar row min height in order for spinner to become visible --> look at alternatives for spinner? 


                    // dayItem.append(
                    //     $('<div>').append(
                    //         $('<a>',
                    //             {
                    //                 'href': this.url,
                    //                 'rel': 'popover',
                    //                 'data-original-title': this.title,
                    //                 'data-content': $('<div>').append(
                    //                     $('<small>').append(
                    //                         $('<em>').append(
                    //                             $('<span>', {'class': 'glyphicon glyphicon-time time'}),
                    //                             ' ' + this.fullTime
                    //                         )
                    //                     ),
                    //                     $('<p>').html(this.content)
                    //                 ).html()
                    //             }
                    //         ).append(
                    //             this.title
                    //         ),
                    //         $('<br>'),
                    //         this.summary
                    //     )
                    // );
                });
                
            }); 
        } else {
            column.html('')
                .append(
                    $('<div>', {'style': 'height: 30px; width: 1px'}),
                    $('<h3>', {'style': 'width: 100%'}).html(data.month)
                   
                ); 
        }; 

            $('a[rel=popover]').popover({'trigger': 'hover', 'html': true, 'container': 'body'});
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

        $('#calendarRow1').html($('#calendarRow2').html())
            .attr('month', $('#calendarRow2').attr('month'));
        $('#calendarRow2').html($('#calendarRow3').html())
            .attr('month', $('#calendarRow3').attr('month'));
        $('#calendarRow3').html($('#calendarRow4').html())
            .attr('month', $('#calendarRow4').attr('month'));

        _loadRow($this, 4, month + '-' + year);
    }

    function _previous($this) {
        var month = $this.data('calendar').month;
        var year = $this.data('calendar').year;

        month = month - 1 < 1 ? 12 : month - 1;
        year = month == 12 ? year - 1 : year;

        $this.data('calendar').month = month;
        $this.data('calendar').year = year;

        $('#calendarRow4').html($('#calendarRow3').html())
            .attr('month', $('#calendarRow3').attr('month'));
        $('#calendarRow3').html($('#calendarRow2').html())
            .attr('month', $('#calendarRow2').attr('month'));
        $('#calendarRow2').html($('#calendarRow1').html())
            .attr('month', $('#calendarRow1').attr('month'));

        _loadRow($this, 1, month + '-' + year);
    }
}) (jQuery);
