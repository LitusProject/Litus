    function updateButton() {
        var enabled = true;
        $('.count').each(function() {
            var max = $(this).attr('data-count');
            if (getLength($(this)) > max)
                enabled = false;
        });
        if (enabled) {
            $("input[type=submit]").removeAttr("disabled");
        } else {
            $("input[type=submit]").attr("disabled", "disabled");
        }
    }

    function addLabel(element) {
        var label = $('.control-label[for=' + element.attr('id') + ']');
        label.append('<br/>(<span class="count-label"></span>)');
        updateLabel(element);
        element.bind("propertychange keyup input paste", function() {
            updateLabel($(this));
        });
    }

    function updateLabel(element) {
        var label = $('.control-label[for=' + element.attr('id') + ']');
        var max = element.attr('data-count');
        var cnt = max - getLength(element);
        var el = label.find('.count-label');
        if (cnt < 0)
            el.addClass('count-negative');
        else
            el.removeClass('count-negative');
        el.html(cnt);
        updateButton();
    }

    function getLength(element) {
        var val = element.val();
        val = val.replace(/\r\n|\r|\n/g,Array(76).join(" ")); // Newlines count as 75 characters
        return val.length;
    }

$(document).ready(function () {
    $('.count').each(function() {
        addLabel($(this));
    });
});
