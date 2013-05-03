(function ($) {
    var defaults = {
        url: '',
        name: '',
        interval: 200,
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
            target: '#output',
            success: function (output) {
                settings.onSubmitted(output);
            },
            error: function(a, b, c) {
                settings.onError();
            }
        });

        _startProgress($this);
    }

    function _startProgress($this) {
        var settings = $this.data('formUploadProgress');
        if (!settings)
            return;

        $.post(settings.url, {upload_id: settings.name}, function (data) {
            if (!data)
                return;
            settings.onProgress(data);
            setTimeout(function () {_startProgress($this);}, settings.interval);
        }, 'json');
    }
}) (jQuery);