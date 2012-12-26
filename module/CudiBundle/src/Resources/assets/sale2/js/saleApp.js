var currentView = 'selectPaydesk';

(function ($) {
    var defaults = {
        socketName: 'saleApp',
        socketUrl: '',
        sessionId: 0,
        authKey: '',
        barcodePrefix: 0,

        tPaydeskSelectTitle: 'Select Paydesk',
        tPaydeskChoose: 'Choose',
        tErrorTitle: 'Error',
        tErrorSocket: 'An error occurred while loading the queue.',

        paydesks: [],
        translateStatus: function (status) {return status},
    };

    var firstAction = true;
    var queue = null;
    var collect = null;
    var sale = null;

    var methods = {
        init : function (options) {
            var settings = $.extend(defaults, options);
            var $this = $(this);
            $(this).data('saleAppSettings', settings);

            _init($this);
            return this;
        }
    };

    $.fn.saleApp = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.saleApp');
        }
    };

    function _init($this) {
        var settings = $this.data('saleAppSettings');

        queue = $.queue({
            barcodePrefix: settings.barcodePrefix,
            translateStatus: settings.translateStatus,
            sendToSocket: function (command) {
                $.webSocket('send', {name: settings.socketName, text: command});
            },
        });

        collect = $this.collect({
            saveComment: function (id, comment) {
                $.webSocket('send', {name: settings.socketName, text:
                    JSON.stringify({
                        'command': 'action',
                        'action': 'saveComment',
                        'id': id,
                        'comment': comment,
                    })
                });
            },
            showQueue: function () {
                queue.queue('show', {permanent: false});
            },
            cancel: function (id) {
                $.webSocket('send', {name: settings.socketName, text:
                    JSON.stringify({
                        'command': 'action',
                        'action': 'cancelCollecting',
                        'id': id,
                    })
                });
                collect.collect('hide');
                queue.queue('show');
            },
        });

        $('body').barcodeControl({
            onBarcode: function (barcode) {
                if (currentView == 'queue')
                    queue.queue('gotBarcode', barcode);
            }
        });

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
                } else if (data.collect) {
                    queue.queue('hide');
                    collect.collect('show', data.collect);
                }
            },
            error: function (e) {
                firstAction = false;

                _socketError($this);
            }
        });
    }

    function _selectPaydesk($this) {
        var settings = $this.data('saleAppSettings');

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
        });

        $('body').append(modal);
        modal.permanentModal();
        $('.modal, .modal-backdrop').addClass('fade');
    }

    function _socketError($this) {
        var settings = $this.data('saleAppSettings');

        $('.modal, .modal-backdrop').removeClass('fade');
        $('.modal').modal('hide');
        $('.modal-backdrop').remove();

        $this.html('').append(
            $('<div>', {'class': 'flashmessage alert alert-error fade'}).append(
                $('<div>', {'class': 'title'}).html(settings.tErrorTitle),
                $('<div>', {'class': 'content'}).append('<p>').html(settings.tErrorSocket)
            ).addClass('in')
        );
    }
})(jQuery);