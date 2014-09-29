(function ($) {
    var showHelp = false;
    var helpButton;

    $(document).ready(function () {
        $('body').append(
            helpButton = $('<div>', {'id': 'toggleHelpButton'}).append(
                $('<div>').html('Help').css({
                    'float': 'right',
                    'margin-right': '5px',
                    'background': '#dddcdc',
                    'color': '#7f7c7c',
                    'padding': '5px',
                    'cursor': 'pointer',
                    'border': '1px solid #000',
                    'border-top': 'none',
                    'z-index': 1000
                }).click(toggleHelp)
            ).hide()
        );

        helpButton.toggle($('[data-help]:visible').length > 0);

        $(document).click(function () {
            helpButton.toggle($('[data-help]:visible').length > 0);
        });

        function toggleHelp() {
            if (showHelp) {
                $('.help-question-mark').animate({'opacity': 0}, 500, function () {$(this).remove()})
            } else {
                $('.help-question-mark').remove();
                $('[data-help]:visible').each(function () {
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
            var modal = $('<div>', {'class': 'modal fade', 'tabindex': '-1'}).append(
                $('<div>', {'class': 'modal-dialog'}).append(
                    $('<div>', {'class': 'modal-content'}).append(
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
                    )
                )
            );
            $('body').append(modal);
            modal.modal();
        }
    });
}) (jQuery);
