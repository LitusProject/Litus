(function ($) {
    var defaults = {
        socketName: 'showQueue',
        modal: null,
        data: {},
        statusTranslate: function () {}
    };

    var methods = {
        cancel: function () {
            _cancel($(this));
            return this;
        },
        close : function () {
            _close($(this));
            return this;
        },
        conclude : function () {
            _conclude($(this));
            return this;
        },
        gotBarcode : function (value) {
            _gotBarcode($(this), value);
            return this;
        },
        init : function (options) {
            var settings = $.extend(defaults, options);
            var $this = $(this);
            $(this).data('collectingSettings', settings);

            _init($this);
            return this;
        }
    };

    $.fn.collecting = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.collecting');
        }
    };

    function _addActions ($this) {
        var articles = $this.find('.articles tr');

        articles.find('.addArticle').click(function () {
            var row = $(this).parent().parent();
            var info = row.data('info');

            if (info.currentNumber < info.number)
                _setArticleNumber($this, row, info.currentNumber + 1);
        });

        articles.find('.removeArticle').click(function () {
            var row = $(this).parent().parent();
            var currentNumber = row.data('info').currentNumber;
            _setArticleNumber($this, row, currentNumber > 0 ? currentNumber -1 : 0);
        });
    }

    function _cancel ($this) {
        var settings = $this.data('collectingSettings');
        $.webSocket('send', {name: settings.socketName, text: 'action: cancelCollecting ' + settings.data.collecting.id});
        _close($this);
    }

    function _close ($this) {
        var settings = $this.data('collectingSettings');

        $this.find('.name').html('&nbsp;');
        $this.find('.articles').html('');

        if (settings == undefined)
            return;

        settings.modal.permanentModal('open', {closable: false});
        $this.removeData('collectingSettings');
    }

    function _conclude ($this) {
        var settings = $this.data('collectingSettings');
        var data = {
            id: settings.data.collecting.id,
            articles: {}
        };
        $this.find('.articles tr:not(.inactive)').each(function () {
            data.articles[$(this).data('info').id] = $(this).data('info').currentNumber;
        });

        $.webSocket('send', {name: settings.socketName, text: 'action: saveCollecting ' + JSON.stringify(data)});
        $.webSocket('send', {name: settings.socketName, text: 'action: stopCollecting ' + settings.data.collecting.id});
        _close($this);
    }

    function _createRow (data, translate) {
        data.currentNumber = 0;
        var row = $('<tr>')
            .append(
                $('<td>').append(data.barcode),
                $('<td>').append(data.title),
                $('<td>').append(translate(data.status)),
                $('<td>').append(
                    $('<span>', {class: 'currentNumber'}).html('0'),
                    '/' + data.number
                ),
                $('<td class="price">').append('&euro;' + (0).toFixed(2)),
                actions = $('<td>', {class: 'actions'})
            );

        if ("booked" == data.status) {
            row.addClass('inactive');
        } else {
            actions.append(
                $('<button>', {class: 'btn btn-success addArticle'}).html('Add'),
                $('<button>', {class: 'btn btn-danger hide removeArticle'}).html('Remove')
            );
        }

        row.data('info', data);

        return row;
    }

    function _gotBarcode ($this, value) {
        $this.find('.articles tr').each(function () {
            for (var i = 0 ; i < $(this).data('info').barcodes.length ; i++) {
                if ($(this).data('info').barcodes[i] == value && $(this).data('info').currentNumber < $(this).data('info').number) {
                    $(this).find('.addArticle').click();
                    return false;
                }
            }
            $this.find('#barcodeFailure').addClass('in');
        });
    }

    function _init ($this) {
        var settings = $this.data('collectingSettings');

        settings.modal.permanentModal('hide');
        $this.find('.cancelCollecting, .concludeCollecting, .showQueue').removeAttr('data-dismiss');

        $this.find('.cancelCollecting').unbind('click').click(function () {
            $this.find('#modalCancelCollecting').modal();
            $this.find('#modalCancelCollecting .confirmCancel').click(function () {
                $this.find('#modalCancelCollecting').modal('hide');
                $this.collecting('cancel');
            });
        });

        $this.find('.concludeCollecting').unbind('click').click(function () {
            $this.collecting('conclude');
        });

        $this.find('.customer .name').html(settings.data.collecting.person.name);

        var articles = $this.find('.articles');
        articles.html('');

        $(settings.data.collecting.articles).each(function () {
            articles.append(_createRow(this, settings.statusTranslate));
        });

        _addActions($this);
    }

    function _setArticleNumber ($this, article, number) {
        var info = article.data('info');
        article.data('info').currentNumber = number;
        article.find('.currentNumber').html(number);

        number == info.number ?
            article.find('.addArticle').addClass('hide'):
            article.find('.addArticle').removeClass('hide');

        0 == number ?
            article.find('.removeArticle').addClass('hide'):
            article.find('.removeArticle').removeClass('hide');
    }
}) (jQuery);