(function($) {
    var defaults = {
        fetchUrl: '',
        moveUrl: '',
        editable: false,
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        buttonText: {},
        editable: false,
        loadError: function () {},
        removeError: function () {},
        addError: function () {},
        updateError: function () {},
        updateSuccess: function () {},
        hideErrors: function () {},
    };

    var methods = {
        init: function (options) {
            var settings = $.extend(defaults, options);

            $(this).data('logisticsCalendar', settings);
            _init($(this));

            return this;
        }
    };

    $.fn.logisticsCalendar = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.logisticsCalendar');
        }
    }

    function _init($this) {
        var settings = $this.data('logisticsCalendar');

        $this.fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true,
            disableResizing: false,
            disableDragging: false,
            slotMinutes: 15,
            allDaySlot: false,
            allDayDefault: false,
            firstHour: 7,
            firstDay: 1,
            lazyFetching: false,
            defaultView: 'agendaWeek',
            timeFormat: {
                agenda: 'H:mm{ - H:mm}',
                '': 'H:mm'
            },
            columnFormat: {
                month: 'ddd',    // Mon
                week: 'ddd d/M', // Mon 9/7
                day: 'dddd d/M'  // Monday 9/7
            },
            axisFormat: 'H:mm',
            monthNames: settings.monthNames,
            monthNamesShort: settings.monthNamesShort,
            dayNames: settings.dayNames,
            dayNamesShort: settings.dayNamesShort,
            buttonText: settings.buttonText,

            eventSources: [
                {
                    events: function (start, end, callback) {
                        _getEvents($this, start, end, callback);
                    }
                }
            ],

            loading: function(isLoading, view) {
                if (isLoading) {
                    $this.addClass('loading');
                } else {
                    $this.removeClass('loading');
                }
            },

            selectable: settings.editable,
            select: function (startDate, endDate, allDay, jsEvent, view) {
                if (!settings.editable)
                    return;
                _addEvent($this, startDate, endDate, allDay, jsEvent, view);
            },
            eventDrop: function (event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view) {
                if (!settings.editable)
                    return;
                _movedEvent($this, event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view);
            },
            eventResize: function (event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) {
                if (!settings.editable)
                    return;
                _resizedEvent($this, event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view);
            },
            eventClick: function (event, jsEvent, view) {
                _clickedEvent($this, event, jsEvent, view);
            }
        });
    }

    function _getEvents($this, start, end, callback) {
        var settings = $this.data('logisticsCalendar');
        settings.hideErrors();

        start = Math.round(start.getTime() / 1000);
        end = Math.round(end.getTime() / 1000);

        $.post(settings.fetchUrl + start + '/' + end, function (data) {
            if (data && data.status == 'success') {
                reservations = data.reservations;

                var events = [];
                var firstHour = 24;

                for (index in reservations) {
                    var reservation = reservations[index];
                    firstHour = Math.min(firstHour, new Date(reservation.start*1000).getHours());

                    events.push({
                        title: reservation.reason,
                        start: reservation.start,
                        end: reservation.end,
                        color: reservation.driver.color,

                        driver: reservation.driver.name,
                        passenger: reservation.passenger,
                        load: reservation.load,
                        additional: reservation.additionalInfo,
                        dbid: reservation.id,
                    });
                }

                callback(events);

                $this.fullCalendar('option', 'firstHour', firstHour);
            } else {
                settings.loadError();
            }
        }, 'json').error(settings.loadError);
    }

    function _addEvent($this, startDate, endDate, allDay, jsEvent, view) {
        console.log('add Event');
    }

    function _movedEvent($this, event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view) {
        var settings = $this.data('logisticsCalendar');
        $.post(settings.moveUrl + event.dbid, {start: Math.round(event.start.getTime() / 1000), end: Math.round(event.end.getTime() / 1000)}, function (data) {
            if (data && data.status == 'success') {
                settings.updateSuccess();
            } else {
                settings.updateError();
            }
        }, 'json').error(settings.updateError);
    }

    function _resizedEvent($this, event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) {
        var settings = $this.data('logisticsCalendar');
        $.post(settings.moveUrl + event.dbid, {start: Math.round(event.start.getTime() / 1000), end: Math.round(event.end.getTime() / 1000)});
    }

    function _clickedEvent($this, event, jsEvent, view) {
        console.log('clicked Event');
        console.log(event);
    }
}) (jQuery);