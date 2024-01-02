(function ($) {
    var defaults = {
        onProgress: function () {},
        onSubmitted: function () {},
        onSubmit: function () {},
        onError: function () {},
    };

    var methods = {
        init: function (options) {
            var label = $(this).closest('.form-group').find('label');
            if(label[0].innerHTML.indexOf('<span class="count-label"') == -1) {
                label.append('(<span class="count-label"></span>)');
            }
            _updateLabel($(this));
            $(this).unbind('propertychange keyup input paste change').bind('propertychange keyup input paste change', function() {
                _updateLabel($(this));
            });

            return this;
        }
    };

    $.fn.fieldCount = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.formUploadProgress');
        }
    };

    function _updateLabel(element) {
        var label = element.closest('.form-group').find('label');
        var cnt = _getRemaining(element);
        var el = label.find('.count-label');
        if (cnt < 0)
            el.addClass('count-negative');
        else
            el.removeClass('count-negative');
        el.html(cnt);
        _updateButton();
    }

    function _updateButton() {
        var enabled = true;
        $('.count').each(function() {
            if (_getRemaining($(this)) < 0) {
                enabled = false;
                $(this).closest('.form-group').addClass('error');
            } else {
                if ($(this).closest('.form-group').find('.help-block ul li').length == 0) {
                    $(this).closest('.form-group').removeClass('error');
                }
            }
        });
        if (enabled) {
            $("input[type=submit]").removeAttr("disabled");
        } else {
            $("input[type=submit]").attr("disabled", "disabled");
        }
    }

    function _getRemaining(element) {
        if (element.is("[data-count]")) {
            var max = element.attr('data-count');
            var val = element.val();
            val = val.replace(/\r\n|\r|\n/g,Array(76).join(" ")); // Newlines count as 75 characters
            return max - val.length;
        } else {
            var lines = element.val().split(/\r\n|\r|\n/g);
            var linelen = element.attr('data-linelen');
            var nrlines = element.attr('data-linecount');
            var len = 0;
            for (var i = lines.length - 2; i >= 0; i--) {
                var line = lines[i];
                len = len + (Math.ceil((line.length == 0 ? 1 : line.length / linelen))) * linelen;
            };
            len = len + lines[lines.length - 1].length;
            return Math.round(nrlines * linelen - len);
        }
    }
}) (jQuery);

$(document).ready(function () {
    // For some reason, this gets called twice, causing the .count fields to have the count displayed twice.
    // This is fixed with a check if the element contains the string '<span class="count-label"'.
    $('.count').each(function() {
        $(this).fieldCount();
    });
});
