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
                _stopProgress($this);
            },
            error: function(a, b, c) {
                settings.onError();
                _stopProgress($this);
            }
        });

        _startProgress($this);
    }

    function _stopProgress($this) {
        clearTimeout($this.data('formUploadProgress').timer);
        if ($this.data('formUploadProgress').request)
            $this.data('formUploadProgress').request.abort();
    }

    function _startProgress($this) {
        var settings = $this.data('formUploadProgress');
        if (!settings)
            return;

        $this.data('formUploadProgress').request = $.post(settings.url, {upload_id: settings.name}, function (data) {
            if (!data)
                return;
            settings.onProgress(data);
            $this.data('formUploadProgress').timer = setTimeout(function () {_startProgress($this);}, settings.interval);
        }, 'json');
    }
}) (jQuery);