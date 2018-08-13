(function($) {
    var defaults = {
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        buttonText: {},
        loadError: function () {},
        hideErrors: function () {},
        tStartDate: 'Start Date',
        tEndDate: 'End Date',
    };

    var methods = {
        init: function (options) {
            var settings = $.extend(defaults, options);

            $(this).data('eventsCalendar', settings);
            _init($(this));

            return this;
        },
        gotoDate: function (year, month, day) {
            $(this).fullCalendar('gotoDate', year, month-1, day);
            return this;
        }
    };

    $.fn.eventsCalendar = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.eventsCalendar');
        }
    };

    function _init($this) {
        var settings = $this.data('eventsCalendar');

        $this.fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: false,
            weekends:false,
            disableResizing: false,
            disableDragging: false,
            slotMinutes: 15,
            allDaySlot: false,
            allDayDefault: false,
            firstHour: 7,
            firstDay: 1,
            lazyFetching: false,
            unselectAuto: false,
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

            selectable: false,
            eventClick: function (event, jsEvent, view) {
                _clickedEvent($this, event, jsEvent, view);
            }
        });
    }

    function _getEvents($this, start, end, callback) {

        var settings = $this.data('eventsCalendar');
        settings.hideErrors();

        start = Math.round(start.getTime() / 1000);
        end = Math.round(end.getTime() / 1000);

        $.post(settings.fetchUrl + start + '/' + end, function (data) {
            if (data && data.status == 'success') {
                var eventlist = data.events;

                var events = [];
                var firstHour = 24;

                for (var index in eventlist) {
                    var event = eventlist[index];
                    firstHour = Math.min(firstHour, new Date(event.start*1000).getHours());

                    events.push({
                        title: event.title,
                        start: event.start,
                        end: event.end,
                        color: "#0000CD",
                        dbid: event.id
                    });
                }

                callback(events);

                $this.fullCalendar('option', 'firstHour', firstHour);
            } else {
                settings.loadError();
            }
        }, 'json').error(settings.loadError);
    }

    function _clickedEvent($this, event, jsEvent, view) {
        var settings = $this.data('eventsCalendar');

        var content = $('<div>').append(
            $('<dl>', {'class': 'dl-horizontal'}).append(
                $('<dt>').html(settings.tStartDate),
                $('<dd>').html(_formatDate(event.start)),
                $('<dt>').html(settings.tEndDate),
                $('<dd>').html(_formatDate(event.end))

            )
        );

        if (event.load) {
            content.find('dl').append(
                $('<dt>').html(settings.tLoad),
                $('<dd>').html(event.load)
            );
        }

        if (event.additional) {
            content.find('dl').append(
                $('<dt>').html(settings.tAdditionalInformation),
                $('<dd>').html(event.additional)
            );
        }

        if (event.passenger) {
            content.find('dl').append(
                $('<dt>').html(settings.tPassenger),
                $('<dd>').html(event.passenger)
            );
        }

        if (event.driver) {
            content.find('dl').append(
                $('<dt>').html(settings.tDriver),
                $('<dd>').html(event.driver)
            );
        }

        if (event.car==true) {
            content.find('dl').append(
                $('<dt>').html(settings.tCar),
                $('<dd>').html("&#10004")
            );
        }
        else{
            content.find('dl').append(
            $('<dt>').html(settings.tCar),
            $('<dd>').html(" &#x2718")
            );
        }

        if (settings.editable) {
            content.append(
                '<hr>'
            );

            if (settings.deletable) {
                content.append(
                    $('<a>', {'class': 'delete btn btn-danger pull-right'}).html(settings.tDelete).css('margin-right', '10px')
                );
            }

            content.append(
                $('<a>', {'class': 'edit btn btn-primary pull-right'}).html(settings.tEdit).css('margin-right', '10px')
            );
        }

        if ($this.data('currentPopover'))
            $this.data('currentPopover').popover('destroy');
        $(jsEvent.target).popover({
            placement: _getPopoverPlacement(event.start, view),
            title: $('<div>').append(
                $('<b>', {'class': 'reason'}).html(event.title),
                $('<div>', {'class': 'pull-right'}).append(
                    $('<a>', {'class': 'close'}).html('&times;').click(function () {$(jsEvent.target).popover('destroy')})
                )
            ),
            content: content.html(),
            trigger: 'manual',
            html: true,
            container: 'body'
        });
        $(jsEvent.target).popover('show');
        $this.data('currentPopover', $(jsEvent.target));
        $('.popover .delete').click(function () {
            _deleteEvent($this, event, jsEvent, view);
        });
        $('.popover .edit').click(function () {
            _editEvent($this, event, jsEvent, view);
        });
    }
    
    function _formatDate(date) {
        return $.fullCalendar.formatDate(date, 'dd/MM/yyyy H:mm');
    }

    function _getPopoverPlacement(startDay, view) {
        var placement = 'right';
        if (view.name == 'month') {
            placement = startDay.getDay() < 4 && startDay.getDay() > 0 ? 'right' : 'left';
        } else if (view.name == 'agendaWeek') {
            placement = startDay.getDay() < 4 && startDay.getDay() > 0 ? 'right' : 'left';
        } else if (view.name == 'agendaDay') {
            placement = 'right';
        }
        return placement;
    }
}) (jQuery);
