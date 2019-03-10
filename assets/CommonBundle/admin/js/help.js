(function ($) {
    var showHelp = false;
    var helpButton;

    $(document).ready(function () {
        $('body').append(
            helpButton = $('<div>', {'id': 'help_button_wrapper'}).append(
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
                $('.help_question_mark').animate({'opacity': 0}, 500, function () {
                    $(this).remove()
                })
            } else {
                $('.help_question_mark').remove();
                $('[data-help]:visible').each(function () {
                    $('body').append(
                        helpQuestionMark = $('<i>', {'class': 'fas fa-question-circle help_question_mark'})
                            .animate({'opacity': 1}, 500)
                            .data('text', $(this).data('help'))
                            .click(showHelpModal)
                    );

                    helpQuestionMark.css({
                        'top': $(this).offset().top + ($(this).height() - helpQuestionMark.height())/2,
                        'left': $(this).offset().left + $(this).width()
                    })
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
