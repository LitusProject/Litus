(function ($) {
    var defaults = {
        url: '',
        errorDialog: null,
        ownLaps: null,
        officialLaps: null,
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
                        options.ownLaps.html(data.laps.number.own);
                        options.officialLaps.html(data.laps.number.official);
                    }
                },
                error: function (e) {
                    options.errorDialog.removeClass('hide');
                }
            }
        );
    }

    function _sendToSocket (text) {
        $.webSocket('send', {name: 'showQueue', text: text});
    }
}) (jQuery);