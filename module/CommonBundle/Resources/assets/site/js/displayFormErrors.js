(function ($) {
    $.fn.displayFormErrors = function(errors) {
        var $this = $(this);
        $this.find('.form-group').removeClass('has-error');
        $this.find('ul.errors').remove();
        $.each(errors, function (name, childErrors) {
            if (typeof childErrors == 'string') {
                var list = $this.find('ul.errors');
                if (list.length == 0) {
                    list = $('<ul>', {'class': 'errors'});
                    $this.closest('.form-group').addClass('has-error').find('input').after(
                        $('<div>', {'class': 'help-block'}).append(list)
                    );
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
