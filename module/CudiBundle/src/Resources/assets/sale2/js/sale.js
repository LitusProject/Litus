var currentView = 'selectPaydesk';

(function ($) {
    var defaults = {
        socketName: 'saleApp',
        socketUrl: '',
        sessionId: 0,
        authKey: '',

        tPaydeskSelectTitle: 'Select Paydesk',
        tPaydeskChoose: 'Choose',
        tErrorTitle: 'Error',
        tErrorSocket: 'An error occurred while loading the queue.',

        paydesks: [],
    };

    var currentModal = null;
    var firstAction = true;
    var queue = null;

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            var $this = $(this);
            $(this).data('saleSettings', settings);

            _init($this);
            return this;
        }
    };

    $.fn.sale = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.sale');
        }
    };

    function _init($this) {
        var settings = $this.data('saleSettings');

        queue = $.queue();

        $.webSocket({
            name: settings.socketName,
            url: settings.socketUrl,
            open: function (e) {
                $('.flashmessage').remove();
                _selectPaydesk($this);

                firstAction = false;
            },
            message: function (e, data) {
                if (data.queue) {
                    queue.queue('updateQueue', data.queue);
                }
            },
            error: function (e) {
                firstAction = false;

                _socketError($this);
            }
        });
    }

    function _selectPaydesk($this) {
        var settings = $this.data('saleSettings');

        var modal = $('<div>', {'class': 'modal ' + (firstAction ? '' : 'fade')}).append(
            $('<div>', {'class': 'modal-header'}).append(
                $('<h3>').html(settings.tPaydeskSelectTitle)
            ),
            body = $('<div>', {'class': 'modal-body'}),
            $('<div>', {'class': 'modal-footer'})
        );

        $(settings.paydesks).each(function () {
            body.append(
                $('<div>', {'class': 'span2'}).append(
                    $('<h3>').html(this.name),
                    $('<br>'),
                    $('<button>', {'class': 'btn'}).data('code', this.code).html(settings.tPaydeskChoose)
                ).css('text-align', 'center')
            );
        });

        body.find('button').click(function () {
            $.webSocket('send', {name: settings.socketName, text:
                JSON.stringify({
                    'command': 'initialize',
                    'queueType': 'queueList',
                    'session': settings.sessionId,
                    'paydesk': $(this).data('code'),
                    'key': settings.authKey,
                })
            });
            modal.permanentModal('hide');

            queue.queue('show');
            currentModal = queue;
        });

        $this.append(modal);
        modal.permanentModal();
        $('.modal, .modal-backdrop').addClass('fade');
        currentModal = modal;
    }

    function _socketError($this) {
        var settings = $this.data('saleSettings');

        if (currentModal) {
            $('.modal, .modal-backdrop').removeClass('fade');
            currentModal.modal('hide');
        }

        $this.html('').append(
            $('<div>', {'class': 'flashmessage alert alert-error fade'}).append(
                $('<div>', {'class': 'title'}).html(settings.tErrorTitle),
                $('<div>', {'class': 'content'}).append('<p>').html(settings.tErrorSocket)
            ).addClass('in')
        );
    }
})(jQuery);