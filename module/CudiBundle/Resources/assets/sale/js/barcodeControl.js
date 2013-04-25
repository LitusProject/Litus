(function ($) {
    var defaults = {
        clearTime: 50,
        barcodeLength: 12,
        onBarcode: function () {},
    };

    var methods = {
        append : function (value) {
            _append($(this), value);
            return this;
        },
        clear : function () {
            _clear($(this));
            return this;
        },
        complete : function () {
            _complete($(this));
            return this;
        },
        init : function (options) {
            var settings = $.extend(defaults, options);
            var $this = $(this);

            $(this).data('barcodeControlSettings', settings);
            _clear($(this));

            $('body').unbind('keydown.barcodeControl').bind('keydown.barcodeControl', function (e) {
                if (e.target == undefined || $(e.target).is('input') && $(e.target).is(':visible')) {
                    return;
                }

                if (_isNumericKey(e.which)) {
                    e.preventDefault();
                    _append($this, _getNumericValue(e.which));
                } else if (e.which == 13) {
                    e.preventDefault();
                    _complete($this);
                } else if (e.which != 16) {
                    _clear($this);
                }
            });

            return this
        },
        isBarcode : function () {
            return _isBarcode($(this));
        },
        option : function (options) {
            if (! $(this).data('barcodeControlSettings'))
                return false;

            $(this).data('barcodeControlSettings', $.extend($(this).data('barcodeControlSettings'), options));
            return true;
        },
        read : function () {
            return _read($(this));
        },
    };

    $.fn.barcodeControl = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.barcodeControl');
        }
    };

    function _append ($this, value) {
        if (undefined == $this.data('barcodeControl'))
            return;

        value = ($this.data('barcodeControl').buffer * 10 + value).toString();
        $this.data('barcodeControl').buffer = value.substr(Math.max(0, value.length - $this.data('barcodeControlSettings').barcodeLength - 1));

        if ($this.data('barcodeControl').timer)
            clearTimeout($this.data('barcodeControl').timer);

        $this.data('barcodeControl').isBarcode = true;

        $this.data('barcodeControl').timer = setTimeout(function () {
            _clear($this);
        }, $this.data('barcodeControlSettings').clearTime);
    }

    function _clear ($this) {
        if ($this.data('barcodeControl'))
            clearTimeout($this.data('barcodeControl').timer);

        $this.data('barcodeControl', {
            timer: null,
            buffer: '',
            isBarcode: false,
        });
    }

    function _complete($this) {
        if (undefined == $this.data('barcodeControlSettings'))
            return;

        if (_read($this).length == $this.data('barcodeControlSettings').barcodeLength)
            $this.data('barcodeControlSettings').onBarcode(_read($this));
        else if (_read($this).length == $this.data('barcodeControlSettings').barcodeLength + 1)
            $this.data('barcodeControlSettings').onBarcode(_read($this).toString().substr(0, $this.data('barcodeControlSettings').barcodeLength));

        _clear($this);
    }

    function _getNumericValue(keyCode) {
        if (! _isNumericKey(keyCode))
            return;

        if (keyCode <= 57)
            return (keyCode - 48);
        else
            return (keyCode - 96);
    }

    function _isBarcode($this) {
        if (undefined == $this.data('barcodeControl'))
            return false;

        return $this.data('barcodeControl').isBarcode;
    }

    function _isNumericKey(keyCode) {
        return (keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105);
    }

    function _read ($this) {
        if (undefined == $this.data('barcodeControl'))
            return 0;

        return $this.data('barcodeControl').buffer;
    }
}) (jQuery);