    function updateButton() {
        var enabled = true;
        $('.count').each(function() {
            if (getRemaining($(this)) < 0) {
                enabled = false;
                $(this).closest('.control-group').addClass('error');
            } else {
                $(this).closest('.control-group').removeClass('error');
            }
        });
        if (enabled) {
            $("input[type=submit]").removeAttr("disabled");
        } else {
            $("input[type=submit]").attr("disabled", "disabled");
        }
    }

    function addLabel(element) {
        var label = $('.control-label[for=' + element.attr('id') + ']');
        label.append('(<span class="count-label"></span>)');
        updateLabel(element);
        element.bind("propertychange keyup input paste", function() {
            updateLabel($(this));
        });
    }

    function updateLabel(element) {
        var label = $('.control-label[for=' + element.attr('id') + ']');
        var cnt = getRemaining(element);
        var el = label.find('.count-label');
        if (cnt < 0)
            el.addClass('count-negative');
        else
            el.removeClass('count-negative');
        el.html(cnt);
        updateButton();
    }

    function getRemaining(element) {
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

$(document).ready(function () {
    $('.count').each(function() {
        addLabel($(this));
    });
});
