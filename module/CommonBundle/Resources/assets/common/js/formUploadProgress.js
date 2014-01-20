(function ($) {
    var defaults = {
        onProgress: function () {},
        onSubmitted: function () {},
        onSubmit: function () {},
        onError: function () {},
    };

    var methods = {
        init: function (options) {
            var settings = $.extend(defaults, options);

            $(this).data('formUploadProgress', settings);
            _init($(this));

            return this;
        }
    };

    $.fn.formUploadProgress = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' +  method + ' does not exist on $.formUploadProgress');
        }
    };

    function _init($this) {
        var settings = $this.data('formUploadProgress');

        $this.on('submit', function (e) {
            e.preventDefault();
            _startUpload($this);
        });
    }

    function _startUpload($this)
    {
        var settings = $this.data('formUploadProgress');
        if (!settings)
            return;

        settings.onSubmit();

        $this.ajaxSubmit({
            success: function (output) {
                settings.onSubmitted(output);
            },
            error: function(a, b, c) {
                settings.onError();
            },
            uploadProgress: function (event, position, total, percentComplete) {
                settings.onProgress({total: total, current: position, percentage: percentComplete});
            },
            dataType: 'json'
        });
    }
}) (jQuery);