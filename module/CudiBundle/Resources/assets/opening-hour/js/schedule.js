(function ($) {
    var defaults = {
        startHour: 8,
        endHour: 19,
        intervalHour: 1,
        timelineWidth: 700,
        days: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        data: []
    };

    var methods = {
        init : function (options) {
            options = $.extend({}, defaults, options);
            $(this).data('schedule', options);
            _init(options, this);

            return this;
        }
    };

    $.fn.schedule = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.schedule');
        }
    };

    function _init(options, schedule) {
        schedule.html('')
            .addClass('schedule')
            .append(
                header = $('<div>', {'class': "schedule-header"})
            );

        var hourWidth = options.timelineWidth / ( options.endHour - options.startHour );
        for(var i = options.startHour ; i <= options.endHour/options.intervalHour ; i+=options.intervalHour) {
            header.append(
                splitter = $('<div>', {'class': "schedule-splitter"})
                    .css('left', (10+hourWidth*(i-options.startHour))),
                label = $('<div>', {'class': "schedule-label"})
                    .css('left', (hourWidth*(i-options.startHour)))
                    .html(i)
            );
        }

        for(var i = 0 ; i < 7 ; i++) {
            if (i >= 5 && !options.weekend)
                break;
            schedule.append(
                $('<div>', {'class': "schedule-day day" + i}).append(
                    $('<div>', {'class': "schedule-label"})
                        .html(options.days[i]),
                    $('<div>', {'class': "schedule-timeline"})
                        .css('width', options.timelineWidth)
                )
            );
        }
        _show(options, schedule);
    }

    function _show(options, schedule) {

        var hourWidth = options.timelineWidth / ( options.endHour - options.startHour );

        for (var i = 0 ; i < 5 ; i++) {
            var timeline = schedule.find('.day' + i + ' > .schedule-timeline');
            if (options.data[i]) {
                if (options.data[i] != '') {
                    $(options.data[i]).each(function () {
                        var block = $('<div>', {'class': "schedule-period"})
                            .css({
                                'width': (this.endTime.hours + this.endTime.minutes / 60 - this.startTime.hours - this.startTime.minutes / 60 ) * hourWidth,
                                'left': (this.startTime.hours + this.startTime.minutes / 60 - options.startHour ) * hourWidth
                            });
                        block.attr('title', this.startTime.text + ' - ' + this.endTime.text).html('&nbsp;' + this.comment);
                        timeline.append(block);
                    });
                } else {
                    var block = $('<div>', {'class': "schedule-period closed"}).html(options.closedText);
                    timeline.append(block);
                }
            }
        }
    }
}) (jQuery);