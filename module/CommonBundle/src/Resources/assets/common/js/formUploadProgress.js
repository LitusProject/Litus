(function ($) {
    var defaults = {
        url: '',
        name: '',
        uploadProgressName: '',
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

        $this.prepend($('<input>', {type: 'hidden', name: settings.uploadProgressName, value: settings.name}));

        $('body').append(frame = $('<iframe>', {name: 'upload_' + settings.name, id: 'upload_' + settings.name}));
        frame.css('display', 'none');
        $this.attr('target', 'upload_' + settings.name);
        $this.submit(function () {_startUpload($this)});

        if ($.browser.msie)
            frame.bind('readystatechange', function (e) {_completeUpload($this, e)});
        else
            frame.bind('load', function (e) {_completeUpload($this, e)});
    }

    function _completeUpload($this, e)
    {
        var settings = $this.data('formUploadProgress');
        if (!settings)
            return;

        if (e.type && ( e.type == 'load' || e.type == "readystatechange")) {
            try {
                var output = $.parseJSON($('#upload_' + settings.name).contents().find('body').html());
                settings.onSubmitted(output);
            } catch (err) {
                settings.onError();
            }
        }
    }

    function _startUpload($this)
    {
        var settings = $this.data('formUploadProgress');
        if (!settings)
            return;

        settings.onSubmit();
        _load($this);
    }

    function _load($this) {
        var settings = $this.data('formUploadProgress');
        if (!settings)
            return;

        $.post(settings.url, {upload_id: settings.name}, function (data) {
            if (!data)
                return;
            settings.onProgress(data);
            setTimeout(function () {_load($this);}, settings.interval);
        }, 'json');
    }
}) (jQuery);