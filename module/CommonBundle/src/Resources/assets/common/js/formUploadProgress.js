(function ($) {
    var defaults = {
        url: '',
        name: '',
        uploadProgressName: '',
        interval: 200,
        onProgress: function () {}
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
        
        $this.append($('<input>', {type: 'hidden', name: settings.uploadProgressName, value: settings.name}));
        
        $this.submit(function (e) {
            //e.preventDefault();
            
            _load($this);
            setInterval(function () {_load($this);}, settings.interval);
        });
    }
    
    function _load($this) {
        var settings = $this.data('formUploadProgress');
        console.log('load');
        $.post(settings.url, {upload_id: settings.name}, settings.onProgress, 'json');
    }
}) (jQuery);