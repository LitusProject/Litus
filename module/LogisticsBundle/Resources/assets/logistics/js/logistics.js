(function($) {
    var defaults = {
        fetchUrl: '',
        moveUrl: '',
        addUrl: '',
        editUrl: '',
        deleteUrl: '',
        passengerTypeaheadUrl: '',
        editable: false,
        deletable: false,
        form: null,
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        buttonText: {},
        loadError: function () {},
        removeError: function () {},
        removeSuccess: function () {},
        addError: function () {},
        addSuccess: function () {},
        updateError: function () {},
        updateSuccess: function () {},
        hideErrors: function () {},
        tNewReservation: 'New Reservation',
        tEditReservation: 'Edit Reservation',
        tStartDate: 'Start Date',
        tEndDate: 'End Date',
        tLoad: 'Load',
        tAdditionalInformation: 'Additional Information',
        tDriver: 'Driver',
        tPassenger: 'Passenger',
        tDelete: 'Delete',
        tEdit: 'Edit'
    };

    var methods = {
        init: function (options) {
            var settings = $.extend(defaults, options);

            $(this).data('logisticsCalendar', settings);
            _init($(this));

            return this;
        },
        gotoDate: function (year, month, day) {
            $(this).fullCalendar('gotoDate', year, month-1, day);
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
    };

    function _init($this) {
        var settings = $this.data('logisticsCalendar');

        $this.fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: settings.editable,
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
                var reservations = data.reservations;

                var events = [];
                var firstHour = 24;

                for (var index in reservations) {
                    var reservation = reservations[index];
                    firstHour = Math.min(firstHour, new Date(reservation.start*1000).getHours());

                    events.push({
                        title: reservation.reason,
                        start: reservation.start,
                        end: reservation.end,
                        color: reservation.driver.color,

                        driver: reservation.driver.name,
                        driverId: reservation.driver.id,
                        passenger: reservation.passenger,
                        passengerId: reservation.passengerId,
                        load: reservation.load,
                        additional: reservation.additionalInfo,
                        dbid: reservation.id
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
        var settings = $this.data('logisticsCalendar');

        if ($this.data('currentPopover'))
            $this.data('currentPopover').popover('destroy');
        $(jsEvent.target).popover({
            placement: _getPopoverPlacement(startDate, view),
            title: $('<div>').append(
                $('<b>', {'class': 'reason'}).html(settings.tNewReservation),
                $('<div>', {'class': 'pull-right'}).append(
                    $('<a>', {'class': 'close'}).html('&times;').click(function () {
                        $(jsEvent.target).popover('destroy');
                        $this.fullCalendar('unselect');
                    })
                )
            ),
            content: settings.form.html(),
            trigger: 'manual',
            html: true,
            container: 'body'
        });
        $(jsEvent.target).popover('show');
        $this.data('currentPopover', $(jsEvent.target));

        $('.popover .start').val(_formatDate(startDate));
        $('.popover .end').val(_formatDate(endDate));
        $('.popover .reservation_edit').hide();

        $('.popover #passengerSearch').typeaheadRemote(
            {
                source: settings.passengerTypeaheadUrl
            }
        ).change(function(e) {
            if ($(this).data('value')) {
                $('.popover #passengerId').val($(this).data('value').id);
            } else {
                $('.popover #passengerId').val('');
            }
        });

        $('.popover form').ajaxForm({
            url: settings.addUrl,
            success: function (data) {
                if (data && data.status == 'success') {
                    $this.fullCalendar('unselect');
                    settings.addSuccess();
                    if ($this.data('currentPopover'))
                        $this.data('currentPopover').popover('destroy');
                    $('.typeahead').remove();

                    $this.fullCalendar('renderEvent', {
                        title: data.reservation.reason,
                        start: data.reservation.start,
                        end: data.reservation.end,
                        color: data.reservation.driver.color,

                        driver: data.reservation.driver.name,
                        driverId: data.reservation.driver.id,
                        passenger: data.reservation.passenger,
                        passengerId: data.reservation.passengerId,
                        load: data.reservation.load,
                        additional: data.reservation.additionalInfo,
                        dbid: data.reservation.id
                    });
                } else {
                    settings.addError();
                    $('.popover form').find('ul.errors').remove();
                    if (data && data.errors) {
                        for(var element in data.errors) {
                            var list = $('<ul>', {'class': 'errors'});
                            for (var error in data.errors[element])
                                list.append($('<li>').html(data.errors[element][error]));
                            var div = $('<div>', {'class': 'help-inline'}).append(list);
                            $('.popover form').find('#' + element).closest('.control-group').addClass('error').find('.controls').append(div);
                        }
                    }
                }
            },
            error: function(a, b, c) {
                settings.addError();
            },
            dataType: 'json'
        });
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
        $.post(settings.moveUrl + event.dbid, {start: Math.round(event.start.getTime() / 1000), end: Math.round(event.end.getTime() / 1000)}, function (data) {
            if (data && data.status == 'success') {
                settings.updateSuccess();
            } else {
                settings.updateError();
            }
        }, 'json').error(settings.updateError);
    }

    function _clickedEvent($this, event, jsEvent, view) {
        var settings = $this.data('logisticsCalendar');

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

    function _deleteEvent($this, event, jsEvent, view) {
        var settings = $this.data('logisticsCalendar');

        $.post(settings.deleteUrl + event.dbid, function (data) {
            if (data && data.status == 'success') {
                settings.removeSuccess();

                if ($this.data('currentPopover'))
                    $this.data('currentPopover').popover('destroy');

                $this.fullCalendar('removeEvents', event._id);
            } else {
                settings.removeError();
            }
        }, 'json').error(settings.removeError);
    }

    function _editEvent($this, event, jsEvent, view) {
        var settings = $this.data('logisticsCalendar');

        if ($this.data('currentPopover'))
            $this.data('currentPopover').popover('destroy');

        $(jsEvent.target).popover({
            placement: _getPopoverPlacement(event.start, view),
            title: $('<div>').append(
                $('<b>', {'class': 'reason'}).html(settings.tEditReservation),
                $('<div>', {'class': 'pull-right'}).append(
                    $('<a>', {'class': 'close'}).html('&times;').click(function () {$(jsEvent.target).popover('destroy')})
                )
            ),
            content: settings.form.html(),
            trigger: 'manual',
            html: true,
            container: 'body'
        });
        $(jsEvent.target).popover('show');
        $this.data('currentPopover', $(jsEvent.target));

        $('.popover .start').val(_formatDate(event.start));
        $('.popover .end').val(_formatDate(event.end));
        $('.popover .reason').val(event.title);
        $('.popover .load').val(event.load);
        $('.popover .additional').html(event.additional);
        $('.popover .driver').val(event.driverId);
        $('.popover #passengerId').val(event.passengerId);
        $('.popover .passenger').val(event.passenger);
        $('.popover .reservation_add').hide();

        $('.popover #passengerSearch').typeaheadRemote(
            {
                source: settings.passengerTypeaheadUrl
            }
        ).change(function(e) {
            if ($(this).data('value')) {
                $('.popover #passengerId').val($(this).data('value').id);
            } else {
                $('.popover #passengerId').val('');
            }
        });

        $('.popover form').ajaxForm({
            url: settings.editUrl + event.dbid,
            success: function (data) {
                if (data && data.status == 'success') {
                    settings.updateSuccess();
                    if ($this.data('currentPopover'))
                        $this.data('currentPopover').popover('destroy');
                    $('.typeahead').remove();

                    $this.fullCalendar('removeEvents', event._id);
                    $this.fullCalendar('renderEvent', {
                        title: data.reservation.reason,
                        start: data.reservation.start,
                        end: data.reservation.end,
                        color: data.reservation.driver.color,

                        driver: data.reservation.driver.name,
                        driverId: data.reservation.driver.id,
                        passenger: data.reservation.passenger,
                        passengerId: data.reservation.passengerId,
                        load: data.reservation.load,
                        additional: data.reservation.additionalInfo,
                        dbid: data.reservation.id
                    });
                } else {
                    settings.updateError();
                    $('.popover form').find('ul.errors').remove();
                    if (data && data.errors) {
                        for(element in data.errors) {
                            var list = $('<ul>', {'class': 'errors'});
                            for (error in data.errors[element])
                                list.append($('<li>').html(data.errors[element][error]));
                            var div = $('<div>', {'class': 'help-inline'}).append(list);
                            $('.popover form').find('#' + element).closest('.control-group').addClass('error').find('.controls').append(div);
                        }
                    }
                }
            },
            error: function(a, b, c) {
                settings.updateError();
            },
            dataType: 'json'
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