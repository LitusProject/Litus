(function ($) {
	var defaults = {
		socketName: 'showQueue',
		modal: null,
		data: {},
		statusTranslate: function () {}
	};

	var methods = {
		cancel: function () {
			_cancel($(this));
			return this;
		},
		close : function () {
			_close($(this));
			return this;
		},
		conclude : function () {
			_conclude($(this));
			return this;
		},
		gotBarcode : function (value) {
		    _gotBarcode($(this), value);
		    return this;
		},
		init : function (options) {
			var settings = $.extend(defaults, options);
			var $this = $(this);
			$(this).data('saleSettings', settings);

			_init($this);
			return this;
		}
	};

	$.fn.sale = function (method) {
    	if (methods[method]) {
    		return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    	} else if (typeof method === 'object' || ! method) {
    		return methods.init.apply(this, arguments);
    	} else {
    		$.error('Method ' +  method + ' does not exist on $.sale');
    	}
    };

	function _addActions ($this) {
	    var articles = $this.find('.articles tr');

	    articles.find('.addArticle').click(function () {
	    	var row = $(this).parent().parent();
	    	var info = row.data('info');

	    	if (info.currentNumber < info.number)
	    		_setArticleNumber($this, row, info.currentNumber + 1);
	    });

	    articles.find('.removeArticle').click(function () {
	    	var row = $(this).parent().parent();
	    	var currentNumber = row.data('info').currentNumber;
	    	_setArticleNumber($this, row, currentNumber > 0 ? currentNumber -1 : 0);
	    });
	}

	function _cancel ($this) {
	    var settings = $this.data('saleSettings');
	    $.webSocket('send', {name: settings.socketName, text: 'action: cancelSelling ' + settings.data.sale.id});
	    _close($this);
	}

	function _close ($this) {
	    var settings = $this.data('saleSettings');

	    $this.find('.name').html('&nbsp;');
	    $this.find('#totalMoney').html('0.00').data('value', 0);
	    $this.find('.articles').html('');

	    if (settings == undefined)
	    	return;

	    settings.modal.permanentModal('open', {closable: false});
	    $this.removeData('saleSettings');
	}

	function _conclude ($this) {
	    var settings = $this.data('saleSettings');
	    var data = {
	        id: settings.data.sale.id,
	        payMethod: $this.find('#payMethod button.active').data('method'),
	        discount: $this.find('.discounts input:checked').data('type'),
	        articles: {}
	    };
	    $this.find('.articles tr:not(.inactive)').each(function () {
	    	data.articles[$(this).data('info').id] = $(this).data('info').currentNumber;
	    });

	    $.webSocket('send', {name: settings.socketName, text: 'action: concludeSelling ' + JSON.stringify(data)});
	    settings.modal.find('#undoLastSelling').show().unbind('click').click(function () {
	        _undoSelling(settings.socketName, data.id);
	    });
	    _close($this);
	}

	function _createRow (data, translate) {
	    data.currentNumber = 0;
	    var row = $('<tr>', {'id': 'article-' + data.id})
	    	.append(
	    		$('<td>').append(data.barcode),
	    		$('<td>').append(data.title),
	    		$('<td>').append(translate(data.status)),
	    		$('<td>').append(
	    			$('<span>', {class: 'currentNumber'}).html('0'),
	    			'/' + data.number
	    		),
	    		$('<td class="price">').append('&euro;' + (0).toFixed(2)),
	    		actions = $('<td>', {class: 'actions'})
	    	);

	    if ("booked" == data.status) {
	    	row.addClass('inactive');
	    } else {
	    	actions.append(
	    		$('<button>', {class: 'btn btn-success addArticle'}).html('Add'),
	    		$('<button>', {class: 'btn btn-danger hide removeArticle'}).html('Remove')
	    	);
	    }

	    row.data('info', data);

	    return row;
	}

	function _gotBarcode ($this, value) {
		var found = false;
	    $this.find('.articles tr').each(function () {
	    	for (var i = 0 ; i < $(this).data('info').barcodes.length ; i++) {
	    		if ($(this).data('info').barcodes[i] == value && $(this).data('info').currentNumber < $(this).data('info').number) {
	    			found = true;
	    			$(this).find('.addArticle').click();
	            	return false;
	    		}
	    	}
	    });
	    if (!found)
	    	$this.find('#barcodeFailure').addClass('in');
	}

	function _init ($this) {
	    var settings = $this.data('saleSettings');

	    settings.modal.permanentModal('hide');
	    $this.find('.cancelSelling, .concludeSelling, .showQueue').removeAttr('data-dismiss');

	    $this.find('.cancelSelling').unbind('click').click(function () {
	    	$this.find('#modalCancelSelling').modal();
	    	$this.find('#modalCancelSelling .confirmCancel').click(function () {
	    		$this.find('#modalCancelSelling').modal('hide');
	    		$this.sale('cancel');
	    	});
	    });

	    $this.find('#modalConcludeSelling').off('shown').on('shown', function () {
	        $(this).find('#payedMoney').focus();
	    });

	    $this.find('.discounts input').unbind('change').change(function () {
	        _updatePrices($this);
	    });

	    $this.find('.concludeSelling').unbind('click').click(function () {
	    	$this.find('#modalConcludeSelling').modal()
	    	    .find('#payedMoney').calculateChange({
	    	        changeField: $this.find('#modalConcludeSelling #changeMoney'),
	    	        totalMoney: $this.find('#totalMoney').data('value')
	    	    }).focus();
	    	$this.find('#modalConcludeSelling .confirmConclude').unbind('click').click(function () {
	    		$this.find('#modalConcludeSelling').modal('hide');
	    		$this.sale('conclude');
	    	});
	    });

	    $this.find('.customer .name').html(settings.data.sale.person.name);
	    $this.find('.customer .university_identification').html(settings.data.sale.person.university_identification);
	    if (settings.data.sale.person.member) {
	        $this.find('.discounts #discount_member').attr('checked', true);
	    } else {
	        $this.find('.discounts #discount_member').prop('disabled', true);
	        $this.find('.discounts #discount_none').attr('checked', true);
	    }
	    $this.find('#totalMoney').html('0.00').data('value', 0);

	    var articles = $this.find('.articles');
	    articles.html('');

	    $(settings.data.sale.articles).each(function () {
	    	if (this.id == 'membership') {
	    		$this.find('.discounts #discount_member').prop('disabled', false);
	        	$this.find('.discounts #discount_member').attr('checked', true);
	    	}
	    	articles.append(_createRow(this, settings.statusTranslate));
	    });

	    _updatePrices($this);
	    _addActions($this);

	    $(settings.data.sale.articles).each(function () {
            for (var i = 0 ; i < this.collected ; i++)
                $('#article-' + this.id + ' .addArticle').click();
	    });
	}

	function _setArticleNumber ($this, article, number) {
		var info = article.data('info');
		article.data('info').currentNumber = number;
		article.find('.currentNumber').html(number);

		number == info.number ?
			article.find('.addArticle').addClass('hide'):
			article.find('.addArticle').removeClass('hide');

		0 == number ?
			article.find('.removeArticle').addClass('hide'):
			article.find('.removeArticle').removeClass('hide');

		_updateTotalPrice($this);
	}

	function _undoSelling (socketName, id) {
	    $(document).find('#undoLastSelling').hide();

	    var data = {
	        id: id,
	    };
	    $.webSocket('send', {name: socketName, text: 'action: undoSelling ' + JSON.stringify(data)});
	}

	function _updatePrices ($this) {
	    var type = $this.find('.discounts input:checked').data('type');
	    $this.find('.articles tr').each(function () {
	        var data = $(this).data('info');
            price = data.discounts[type] !== undefined ? data.discounts[type] : data.price;
	        $(this).find('.price').html('&euro; ' + (price / 100).toFixed(2));
	        $(this).data('info').currentPrice = price;
	    });
	    _updateTotalPrice($this);
	}

	function _updateTotalPrice ($this) {
	    var total = 0;
	    $this.find('.articles').find('tr:not(.inactive)').each(function () {
	    	var data = $(this).data('info');
	    	total += data.currentNumber * data.currentPrice;
	    });
	    $this.find('#totalMoney').html((total / 100).toFixed(2)).data('value', total).change();
	}
}) (jQuery);