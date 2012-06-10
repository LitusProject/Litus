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
        
        $this.submit(function (e) {
            e.preventDefault();
            
            settings.onSubmit();
                        
            $this.ajaxSubmit({
                dataType: 'json',
                success: settings.onSubmitted,
                error: settings.onError,
            });
            
            _load($this);
        });
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