(function ($) {
    var showHelp = false;
    $(document).ready(function () {
        if ($('[data-help]').length > 0) {
            $('body').append(
                $('<div>', {'id': 'toggleHelpButton'}).html('Help').css({
                    'position': 'fixed',
                    'top': 0,
                    'right': 10,
                    'background': '#dddcdc',
                    'color': '#7f7c7c',
                    'padding': '5px',
                    'cursor': 'pointer',
                    'border': '1px solid #000',
                    'border-top': 'none'
                }).click(toggleHelp)
            );
        }

        function toggleHelp() {
            if (showHelp) {
                $('.help-question-mark').animate({'opacity': 0}, 500, function () {$(this).remove()})
            } else {
                $('.help-question-mark').remove();
                $('[data-help]').each(function () {
                    $('body').append(
                        $('<div>', {'class': 'help-question-mark'}).html('?').css({
                            'width': 20,
                            'height': 20,
                            'margin-top': '-10px',
                            'margin-left': '-10px',
                            'font-size': '16px',
                            'background': '#CB2C2C',
                            'border-radius': 10,
                            'text-align': 'center',
                            'line-height': '20px',
                            'color': '#fff',
                            'cursor': 'pointer',
                            'position': 'absolute',
                            'top': $(this).offset().top,
                            'left': $(this).offset().left + $(this).width(),
                            'opacity': 0
                        }).animate({'opacity': 1}, 500).data('text', $(this).data('help')).click(showHelpModal)
                    );
                });
            }
            showHelp = !showHelp;
        }

        function showHelpModal(e) {
            var modal = $('<div>', {'class': 'modal hide fade'}).append(
                $('<div>', {'class': 'modal-header'}).append(
                    $('<span>').html('Litus Admin'),
                    '/Help'
                ),
                $('<div>', {'class': 'modal-body'}).append(
                    $('<div>').append(
                        $('<p>').html($(e.target).data('text'))
                    ),
                    $('<div>', {'class': 'footer'}).append(
                        $('<input>', {'type': 'button', 'data-dismiss': 'modal', 'value': 'Close'})
                    )
                )
            );
            $('body').append(modal);
            modal.modal();
        }
    });
}) (jQuery);