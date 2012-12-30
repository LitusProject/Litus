(function ($) {
    var defaults = {
        clearTime: 5000,
        changeField: null,
        totalMoney: 0,
    };

    var methods = {
        clear : function () {
            _clear($(this));
            return this;
        },
        init : function (options) {
            var settings = $.extend(defaults, options);
            options.value = 0;
            var $this = $(this);

            $(this).data('calculateChange', settings);
            _init($(this));

            return this;
        },
        destroy : function () {
            $(this).unbind('keydown.calculateChange');
            $(this).removeData('calculateChange');
            return this;
        }
    };

    $.fn.calculateChange = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.calculateChange');
        }
    };

    function _clear ($this) {
        if ($this.data('calculateChange'))
            $this.data('calculateChange').value = 0;

        _clearBuffer($this);
        _update($this);
    }

    function _clearBuffer ($this) {
        if (! $this.data('calculateChange'))
            return;

        clearTimeout($this.data('calculateChange').timer);
        $this.data('calculateChange').timer = null;
        $this.data('calculateChange').value = 0;
    }

    function _getNumericValue(keyCode) {
        if (! _isNumericKey(keyCode))
            return;

        if (keyCode <= 57)
            return (keyCode - 48);
        else
            return (keyCode - 96);
    }

    function _init ($this) {
        _clear($this);

        $this.unbind('keydown.calculateChange').bind('keydown.calculateChange', function (e) {
            e.preventDefault();

            if (e.keyCode == 67 || e.keyCode == 8) { // c or backspace
                _clear($this);
                return;
            }

            if (!_isNumericKey(e.keyCode))
                return;

            $this.data('calculateChange').value = $this.data('calculateChange').value * 10 + _getNumericValue(e.keyCode);
            _update($this);

            if ($this.data('calculateChange').timer)
                clearTimeout($this.data('calculateChange').timer);

            $this.data('calculateChange').timer = setTimeout(function () {
                _clearBuffer($this);
            }, $this.data('calculateChange').clearTime);
        });
    }

    function _isNumericKey(keyCode) {
        return (keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105);
    }

    function _update ($this) {
        var data = $this.data('calculateChange');

        $this.val((data.value / 100).toFixed(2));

        data.value == 0 ?
            data.changeField.val((0).toFixed(2)):
            data.changeField.val(((data.value - data.totalMoney) / 100 ).toFixed(2));
    }
}) (jQuery);