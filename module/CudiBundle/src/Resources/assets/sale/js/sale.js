(function ($) {
	var defaults = {
		socketName: 'showQueue',
		modal: null,
		data: {},
		statusTranslate: function () {}
	};
	
	var methods = {
		init : function (options) {
			var settings = $.extend(defaults, options);
			var $this = $(this);
			$(this).data('saleSettings', settings);

			settings.modal.permanentModal('hide');
			$(this).find('.cancelSelling, .completeSelling, .showQueue').removeAttr('data-dismiss');
			
			$(this).find('.cancelSelling').unbind('click').click(function () {
				$this.find('#modalCancelSelling').modal();
				$this.find('#modalCancelSelling .confirmCancel').click(function () {
					$this.find('#modalCancelSelling').modal('hide');
					$this.sale('cancel');
				});
			});
			
			$(this).find('.concludeSelling').unbind('click').click(function () {
				$this.find('#modalConcludeSelling').modal();
				$this.find('#modalConcludeSelling .confirmConclude').unbind('click').click(function () {
					$this.find('#modalConcludeSelling').modal('hide');
					$this.sale('conclude');
				});
			});
			
			$(this).find('.name').html(settings.data.sale.person.name);
			$(this).find('#payedMoney, #changeMoney, #totalMoney').html('0.00').data('value', 0);
			
			var articles = $(this).find('.articles');
			articles.html('');
			
			$(settings.data.sale.articles).each(function () {
				this.currentNumber = 0;
				var row = $('<tr>')
					.append(
						$('<td>').append(this.barcode),
						$('<td>').append(this.title),
						$('<td>').append(settings.statusTranslate(this.status)),
						$('<td>').append(
							$('<span>', {class: 'currentNumber'}).html('0'),
							'/' + this.number
						),
						$('<td>').append('&euro; ' + (this.price / 100).toFixed(2)),
						actions = $('<td>', {class: 'actions'})
					);
				
				if ("booked" == this.status) {
					row.addClass('inactive');
				} else {
					actions.append(
						$('<button>', {class: 'btn btn-success addArticle'}).html('Add'),
						$('<button>', {class: 'btn btn-danger hide removeArticle'}).html('Remove')
					);
				}
				
				row.data('info', this);
				
				articles.append(row);
			});
			
			articles.find('.addArticle').click(function () {
				var row = $(this).parent().parent();
				var info = row.data('info');
				
				info.currentNumber < info.number ?
					setArticleNumber(row, info.currentNumber + 1) :
					$this.find('#modalUnableToAdd').modal();
			});
			
			articles.find('.removeArticle').click(function () {
				var row = $(this).parent().parent();
				var currentNumber = row.data('info').currentNumber;
				setArticleNumber(row, currentNumber > 0 ? currentNumber -1 : 0);
			});
			
			setArticleNumber = function (article, number) {
				var info = article.data('info');
				article.data('info').currentNumber = number;
				article.find('.currentNumber').html(number);
				
				number == info.number ?
					article.find('.addArticle').addClass('hide'):
					article.find('.addArticle').removeClass('hide');
				
				0 == number ?
					article.find('.removeArticle').addClass('hide'):
					article.find('.removeArticle').removeClass('hide');
				
				updateTotalPrice();
			}
			
			
			updateTotalPrice = function () {
				var total = 0;
				articles.find('tr:not(.inactive)').each(function () {
					var data = $(this).data('info');
					total += data.currentNumber * data.price;
				});
				$this.find('#totalMoney').data('value', total).change();
				$this.find('#totalMoney').html((total / 100).toFixed(2));
			}
			
			return this;
		},
		close : function () {
			var settings = $(this).data('saleSettings');
			
			$(this).find('.name').html('&nbsp;');
			$(this).find('#payedMoney, #changeMoney, #totalMoney').html('0.00').data('value', 0);
			$(this).find('.articles').html('');
			
			if (settings == undefined)
				return;
			
			settings.modal.permanentModal('open');
			$(this).removeData('saleSettings');
			return this;
		},
		cancel: function () {
			var settings = $(this).data('saleSettings');
			$.webSocket('send', {name: settings.socketName, text: 'action: cancelSelling ' + settings.data.sale.id});
			$(this).sale('close');
		},
		conclude : function () {
			var settings = $(this).data('saleSettings');
			var data = {id: settings.data.sale.id, articles: {}};
			$(this).find('.articles tr:not(.inactive)').each(function () {
				data.articles[$(this).data('info').id] = $(this).data('info').currentNumber;
			});
			
			$.webSocket('send', {name: settings.socketName, text: 'action: concludeSelling ' + JSON.stringify(data)});
			$(this).sale('close');
			return this;
		}
	};
	
	$.fn.sale = function (method) {
		if ( methods[ method ] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on $.sale' );
		}
	};
}) (jQuery);