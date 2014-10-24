(function ($) {
    $.fn.displayFormErrors = function(errors) {
        var $this = $(this);
        $this.closest('.row').find('div.errors').html('');
        $.each(errors, function (name, childErrors) {
            if (typeof childErrors == 'string') {
                var list = $this.closest('.row').find('div.errors ul.errors');
                if (list.length == 0) {
                    list = $('<ul>', {'class': 'errors'});
                    $this.closest('.row').find('div.errors').after(list);
                }
                list.append($('<li>').html(childErrors));
            } else {
                var child;
                if ($this.is('form')) {
                    child = $this.find('[name=' + name + ']');
                } else {
                    child = $this.find('[name$="[' + name + ']"]');
                }
                child.displayFormErrors(childErrors);
            }
        });
    };
}) (jQuery);
