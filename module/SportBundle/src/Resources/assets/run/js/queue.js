(function ($) {
    var defaults = {
        url: '',
        errorDialog: null,
        ownLaps: null,
        officialLaps: null,
        removeLapModal: null,
        removeLapSuccess: null,
    };

    var methods = {
        init: function (options) {
            var settings = $.extend(defaults, options);

            $(this).data('runQueueSettings', settings);

            _init($(this));

            return this;
        },
    }

    $.fn.runQueue = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.runQueue');
        }
    };

    function _init ($this) {
        var options = $this.data('runQueueSettings');

        $.webSocket(
            {
                name: 'runQueue',
                url: options.url,
                open: function (e) {
                    options.errorDialog.addClass('hide');
                },
                message: function (e, data) {
                    options.errorDialog.addClass('hide');
                    if (data.laps) {
                        $this.html('');
                        options.ownLaps.html(data.laps.number.own);
                        options.officialLaps.html(data.laps.number.official);

                        $(data.laps.laps).each(function (num, lap) {
                            if (lap == undefined)
                                return;

                            var row = $('<tr>', {'class': 'item item-' + lap.id});
                            row.append(
                                runnerName = $('<td>').html(lap.fullName),
                                $('<td>').html(lap.registrationTime),
                                lapTime = $('<td>'),
                                actions = $('<td>')
                            );

                            if (lap.state == 'previous') {
                                lapTime.html(lap.lapTime);
                            } else if (lap.state == 'current') {
                                lapTime.html($('<i>').html('Running'));
                                runnerName.html('&rarr; ' + runnerName.html());
                            } else {
                                lapTime.html($('<i>').html('Queued'));
                            }

                            if (lap.state != 'previous') {
                                actions.append(
                                    $('<a>', {'class': 'delete', 'href': '#'}).html('Delete').data({
                                        id: lap.id,
                                        runner: lap.fullName
                                    })
                                );
                            }
                            $this.append(row);

                            actions.find('.delete').click(function (e) {
                                e.preventDefault();
                                _removeLap($(this), $this);
                            });
                        });
                    }
                },
                error: function (e) {
                    options.errorDialog.removeClass('hide');
                }
            }
        );
    }

    function _removeLap(button, $this) {
        var options = $this.data('runQueueSettings');

        options.removeLapModal.find('.runner').html(button.data('runner'));
        options.removeLapModal.find('.cancel').one('click', function () {
            options.removeLapModal.modal('hide');
        });
        var id = button.data('id');
        options.removeLapModal.find('.delete').unbind('click').click(function () {
            _sendToSocket('action: deleteLap ' + id);
            $('.flashmessage').addClass('hide');
            options.removeLapSuccess.removeClass('hide');
            $('.item-' + id).remove();
            options.removeLapModal.modal('hide');
        });
        options.removeLapModal.modal();
    }

    function _sendToSocket (text) {
        $.webSocket('send', {name: 'runQueue', text: text});
    }
}) (jQuery);