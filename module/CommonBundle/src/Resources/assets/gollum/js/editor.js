(function ($) {
    var defaults = {
        toolbar: {
            "h1": ["h1", "Header 1", "h1"],
            "h2": ["h2", "Header 2", "h2"],
            "h3": ["h3", "Header 3", "h3"],
            "divider": "-",
            "link": ["link", "Link", "link"],
            "image": ["image", "Image", "download-media"],
            "divider": "-",
            "bold": ["bold", "Bold", "bold"],
            "italic": ["italic", "Italic", "italic"],
            "code": ["code", "Code", "code"],
            "divider": "-",
            "ul": ["ul", "Unordered List", "u-list"],
            "ol": ["ol", "Ordered List", "o-list"],
            "blockquote": ["blockquote", "Blockquote", "quotemark"],
            "hr": ["hr", "Horizontal Rule", "horizontal-rule"],
            "divider": "-",
            "help": ["help", "Help", "help"],
        },
        uploadURL: '',
        progressURL: '',
        uploadProgressName: '',
        progressId: 1,
    };
    
    var methods = {
    	init : function (options) {
    	    this.each(function () {
    	        _init($(this), options);
    	    });
    		return this;
    	},
    	replaceSelection: function (replaceText) {
    	    _replaceFieldSelection($(this), replaceText);
    	    return this;
    	},
    };
    
    $.fn.gollum = function (method) {
    	if (methods[method]) {
    		return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    	} else if (typeof method === 'object' || ! method) {
    		return methods.init.apply(this, arguments);
    	} else {
    		$.error('Method ' +  method + ' does not exist on $.gollum');
    	}
    };
    
    _init = function ($that, options) {
        options = $.extend(defaults, options);
            	    
	    $that.data('gollum-editor', options)
	        .addClass('gollum-editor-body')
	        .wrap($('<div>', {'class': 'gollum-editor'}));

	    var wrapper = $that.closest('.gollum-editor');
	        
	    var functionbar = $('<div>', {'class': 'gollum-editor-function-bar'});
	    wrapper.prepend(functionbar);
	    
	    var buttons = $('<div>', {'class': 'gollum-editor-function-buttons'});
	    functionbar.append(buttons);
	    
	    $.each(options.toolbar, function(key, val){
	        if (key == 'divider')
	            button = $('<span>', {'class': 'function-divider'}).html('&nbsp;');
	        else
	            button = $('<a>', {'href': '#', 'class': 'function-button function-' + val[0], 'title': val[1], 'tabindex': -1})
	                .append($('<span>', {'class': 'mini-icon '+val[2]}))
	                .data('function', 'function-' + val[0]);
	        buttons.append(button);
	    });
	    
	    functionbar.after('<div class="gollum-editor-help"><ul class="gollum-editor-help-parent"><li></li></ul><ul class="gollum-editor-help-list"><li></li></ul><div class="gollum-editor-help-wrapper"><div class="gollum-editor-help-content"><p></p></div></div></div>');
	           

	    functionbar.find('a.function-button').each(function() {
	        if (_getDefinitionFor($(this).data('function'))) {
	            $(this).click(_functionButtonClick);
	            $(this).removeClass('disabled');
	        }
	        else if ($(this).data('function') != 'function-help') {
	            $(this).addClass('disabled');
	        }
	    });
	    
	    _defineHelp(functionbar.find('a.function-help'));
    };
    
    _functionButtonClick = function (e) {
        e.preventDefault();
        var def = _getDefinitionFor($(this).data('function'));
        if (typeof def == 'object') {
            _executeAction($(this).closest('.gollum-editor').find('.gollum-editor-body'), def);
        }
    };
    
    _executeAction = function ($that, definitionObject) {
        var txt = $that.val();
        var options = $that.data('gollum-editor');

        var selPos = _getFieldSelectionPosition($that);
        var selText = _getFieldSelection($that);
        var repText = selText;
        var reselect = true;
        var cursor = null;
        
        if (definitionObject.exec && typeof definitionObject.exec == 'function') {
            definitionObject.exec(txt, selText, $that, options.uploadURL, options.progressURL, options.uploadProgressName, options.progressId);
            return;
        }
        
        var searchExp = /([^\n]+)/gi;
        if (definitionObject.search && typeof definitionObject.search == 'object') {
            searchExp = null;
            searchExp = new RegExp(definitionObject.search);
        }

        if (definitionObject.replace && typeof definitionObject.replace == 'string') {
            var rt = definitionObject.replace;
            repText = repText.replace(searchExp, rt);
            repText = repText.replace(/\$[\d]/g, '');
            
            if (repText === '') {
                cursor = rt.indexOf('$1');
                
                repText = rt.replace(/\$[\d]/g, '');
                
                if (cursor == -1)
                    cursor = Math.floor(rt.length / 2);
            }
        }
        
        if (definitionObject.append && typeof definitionObject.append == 'string') {
            if (repText == selText)
                reselect = false;

            repText += definitionObject.append;
        }
        
        if (repText)
            _replaceFieldSelection($that, repText, reselect, cursor);
    };
    
    _getFieldSelectionPosition = function ($field) {
        if ($field.length) {
            var start = 0, end = 0;
            var el = $field.get(0);
            
            if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
                start = el.selectionStart;
                end = el.selectionEnd;
            } else {
                var range = document.selection.createRange();
                var stored_range = range.duplicate();
                stored_range.moveToElementText(el);
                stored_range.setEndPoint('EndToEnd', range);
                start = stored_range.text.length - range.text.length;
                end = start + range.text.length;
                
                var s = start;
                var lb = 0;
                var i;
                for (i=0 ; i < s ; i++) {
                    if (el.value.charAt(i).match(/\r/))
                        ++lb;
                }
                
                if (lb) {
                    start = start - lb;
                    lb = 0;
                }
                
                var e = end;
                for (i=0 ; i < e ; i++) {
                    if (el.value.charAt(i).match(/\r/))
                        ++lb;
                }
                
                if (lb)
                    end = end - lb;
            }
            
            return {
                start: start,
                end: end
            };
        }
    };
    
    _getFieldSelection = function ($field) {
        var selStr = '';
        var selPos;
        
        if ($field.length) {
            selPos = _getFieldSelectionPosition($field);
            selStr = $field.val().substring(selPos.start, selPos.end);
            return selStr;
        }
        return false;
    };
    
    _replaceFieldSelection = function ($field, replaceText, reselect, cursorOffset) {
        var selPos = _getFieldSelectionPosition($field);
        var fullStr = $field.val();
        var selectNew = true;
        if (reselect === false)
            selectNew = false;
        
        var scrollTop = null;
        if ($field[0].scrollTop)
            scrollTop = $field[0].scrollTop;
        
        $field.val(fullStr.substring(0, selPos.start) + replaceText + fullStr.substring(selPos.end));
        $field[0].focus();
        
        if (selectNew) {
            if ($field[0].setSelectionRange) {
                if (cursorOffset)
                    $field[0].setSelectionRange(selPos.start + cursorOffset, selPos.start + cursorOffset);
                else
                    $field[0].setSelectionRange(selPos.start, selPos.start + replaceText.length);
            } else if ($field[0].createTextRange) {
                var range = $field[0].createTextRange();
                range.collapse(true);
                if (cursorOffset) {
                    range.moveEnd(selPos.start + cursorOffset);
                    range.moveStart(selPos.start + cursorOffset);
                } else {
                    range.moveEnd('character', selPos.start + replaceText.length);
                    range.moveStart('character', selPos.start);
                }
                range.select();
            }
        }
        
        if (scrollTop)
            $field[0].scrollTop = scrollTop;
    };
    
    _getDefinitionFor = function (attr) {
        if (LanguageDefinition[attr] && typeof LanguageDefinition[attr] == 'object')
            return LanguageDefinition[attr];
        
        return null;
    };
    
    _defineHelp = function($that) {
        $that.removeClass('disabled')
            .unbind('click')
            .click(_helpButtonClick);
        
        _generateHelpMenu($that.closest('.gollum-editor').find('.gollum-editor-help'));
    };
    
    _generateHelpMenu = function($that) {
        $that.find('.gollum-editor-help-parent').html('');
        $that.find('.gollum-editor-help-list').html('');
        $that.find('.gollum-editor-help-content').html('');
        
        for (var i=0 ; i < LanguageDefinitionHelp.length ; i++) {
            if (typeof LanguageDefinitionHelp[i] != 'object')
                break;
        
            var $newLi = $('<li><a href="#" rel="' + i + '">' +
            LanguageDefinitionHelp[i].menuName + '</a></li>');
            $that.find('.gollum-editor-help-parent').append($newLi);
            if (i === 0)
                $newLi.children('a').addClass('selected');
            
            $newLi.children('a').click(_helpParentMenuClick);
        }
        
        _generateHelpSubMenu($that, LanguageDefinitionHelp[0], 0);
        $($that.find('.gollum-editor-help-list li a').get(0)).click();
    };
    
    _generateHelpSubMenu = function($that, subData, index) {
        $that.find('.gollum-editor-help-list').html('');
        $that.find('.gollum-editor-help-content').html('');
        for (var i=0 ; i < subData.content.length ; i++) {
            if (typeof subData.content[i] != 'object')
                break;
            
            var $subLi = $('<li><a href="#" rel="' + index + ':' + i + '">' +
            subData.content[i].menuName + '</a></li>');
            
            $that.find('.gollum-editor-help-list').append($subLi);
            $subLi.children('a').click(_helpSubMenuClick);
        }
    };
    
    _hideHelp = function($that) {
        if ($.browser.msie) {
            $that.css('display', 'none');
        } else {
            $that.animate(
                {
                    opacity: 0
                },
                200,
                function() {
                    $that.animate({ height: 'hide' }, 200);
                }
            );
        }
    };
    
    _showHelp = function() {
        if ($.browser.msie) {
            $that.css('display', 'block');
        } else {
            $that.animate(
                {
                    height: 'show'
                },
                200,
                function() {
                    $that.animate({ opacity: 1 }, 300);
                }
            );
        }
    };
    
    _helpButtonClick = function(e) {
        e.preventDefault();
        $that = $(e.target).closest('.gollum-editor').find('.gollum-editor-help');
        if ($that.is(':visible')) {
            _hideHelp($that);
        } else {
            _showHelp($that);
        }
    };
    
    _helpParentMenuClick = function(e) {
        e.preventDefault();
        $that = $(e.target).closest('.gollum-editor').find('.gollum-editor-help');

        if ($(this).hasClass('selected'))
            return;
        
        var helpIndex = $(this).attr('rel');
        var subData = LanguageDefinitionHelp[helpIndex];
        
        $that.find('.gollum-editor-help-parent li a').removeClass('selected');
        $(this).addClass('selected');
        _generateHelpSubMenu($that, subData, helpIndex);
        $($that.find('.gollum-editor-help-list li a').get(0)).click();
    };
    
    _helpSubMenuClick = function(e) {
        e.preventDefault();
        $that = $(e.target).closest('.gollum-editor').find('.gollum-editor-help');

        if ($(this).hasClass('selected'))
            return;
        
        var rawIndex = $(this).attr('rel').split(':');
        $that.find('.gollum-editor-help-list li a').removeClass('selected');
        $(this).addClass('selected');
        _showHelpFor($that, rawIndex[0], rawIndex[1]);
    };
    
    _showHelpFor = function($that, index1, index2) {
      var html = LanguageDefinitionHelp[index1].content[index2].data;
      $that.find('.gollum-editor-help-content').html(html);
    };
}) (jQuery)