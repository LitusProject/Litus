(function ($) {
    $(document).ready(function () {
        $('.info-button').click(showInfoModal);

        function showInfoModal(e) {
            var modal = $('<div>', {'class': 'modal fade', 'tabindex': '-1'}).append(
                $('<div>', {'class': 'modal-dialog'}).append(
                    $('<div>', {'class': 'modal-content'}).append(
                        $('<div>', {'class': 'modal-header'}).append(
                            $('<span>').html('Litus Admin'),
                            '/Help'
                        ),
                        $('<div>', {'class': 'modal-body'}).append(
                            $('<div>').append(
                                $('<p>').html($(e.target).data('info'))
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
