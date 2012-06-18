(function ($) {
    $.fn.collapseActions = function () {
        return this.each(function () {
            $(this).find('td.actions').each(function () {
                if ($(this).find('a').length < 2) {
                    $(this).find('a').show();
                    return;
                }
                var link = $('<a>', {'href': '#', 'class': 'action'}).html('Actions');
                $(this).append(link);
                link.show()
                    .actionsTooltip({placement: 'left'});
            });
        });
    };
    $(document).ready(function () {
        $('table.manage').collapseActions()
            .bind('change', function () {$(this).collapseActions()});
    });
}) (jQuery);

!function ($) {

  "use strict"; // jshint ;_;


 /* ActionsTooltip PUBLIC CLASS DEFINITION
  * =============================== */

  var ActionsTooltip = function (element, options) {
    this.init('actionsTooltip', element, options)
  }

  ActionsTooltip.prototype = {

    constructor: ActionsTooltip

  , init: function (type, element, options) {
      var eventIn
        , eventOut

      this.type = type
      this.$element = $(element)
      this.options = this.getOptions(options)
      this.enabled = true

      if (this.options.trigger != 'manual') {
        eventIn  = this.options.trigger == 'hover' ? 'mouseenter' : 'focus'
        this.$element.on(eventIn, this.options.selector, $.proxy(this.enter, this))
      }

      this.options.selector ?
        (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) : null;
    }

  , getOptions: function (options) {
      options = $.extend({}, $.fn[this.type].defaults, options, this.$element.data())

      if (options.delay && typeof options.delay == 'number') {
        options.delay = {
          show: options.delay
        , hide: options.delay
        }
      }

      return options
    }

  , enter: function (e) {
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (!self.options.delay || !self.options.delay.show) return self.show()

      clearTimeout(this.timeout)
      self.hoverState = 'in'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'in') self.show()
      }, self.options.delay.show)
    }

  , leave: function (e) {
      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, this.tip(), this.$element) :
        this.options.placement;
      if (placement == 'top'
              && e.clientX > this.tip().offset().left && e.clientX < this.tip().offset().left + this.tip().width()
              && e.clientY > this.tip().offset().top && e.clientY < this.tip().offset().top + this.tip().height() + this.$element.height() + 20)
          return;
      else if (placement == 'bottom'
              && e.clientX > this.tip().offset().left && e.clientX < this.tip().offset().left + this.tip().width()
              && e.clientY > this.tip().offset().top - this.$element.height() - 20 && e.clientY < this.tip().offset().top + this.tip().height())
          return;
      else if (placement == 'left'
              && e.clientX > this.tip().offset().left && e.clientX < this.tip().offset().left + this.tip().width() + this.$element.width() + 30
              && e.clientY > this.tip().offset().top && e.clientY < this.tip().offset().top + this.tip().height() + 10)
          return;
      else if (placement == 'right'
              && e.clientX > this.tip().offset().left - this.$element.width() - 20 && e.clientX < this.tip().offset().left + this.tip().width()
              && e.clientY > this.tip().offset().top && e.clientY < this.tip().offset().top + this.tip().height() + 10)
          return;
      
      $(document).off('mousemove.actionsTooltip', $.proxy(this.leave, this))

      if (this.timeout) clearTimeout(this.timeout)
      if (!this.options.delay || !this.options.delay.hide) return this.hide()

      this.hoverState = 'out'
      this.timeout = setTimeout(function() {
        if (this.hoverState == 'out') this.hide()
      }, this.options.delay.hide)
    }

  , show: function () {
      var $tip
        , inside
        , pos
        , actualWidth
        , actualHeight
        , placement
        , tp
        , that
        
      $('.tooltip').remove();

      if (this.enabled) {
        that = this;
        $tip = this.tip()
        this.setContent()

        if (this.options.animation) {
          $tip.addClass('fade')
        }

        placement = typeof this.options.placement == 'function' ?
          this.options.placement.call(this, $tip[0], this.$element[0]) :
          this.options.placement

        inside = /in/.test(placement)

        $tip
          .detach()
          .css({ top: 0, left: 0, display: 'block' })
          .appendTo(inside ? this.$element : document.body)

        pos = this.getPosition(inside)

        actualWidth = $tip[0].offsetWidth
        actualHeight = $tip[0].offsetHeight

        switch (inside ? placement.split(' ')[1] : placement) {
          case 'bottom':
            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'top':
            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'left':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
            break
          case 'right':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
            break
        }
        
        $tip.find('a').click(function () {
            that.hide();
        });

        $tip
          .css(tp)
          .addClass(placement)
          .addClass('in')
        $(document).on('mousemove.actionsTooltip', $.proxy(this.leave, this))
      }
    }

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()
      
      $tip.find('.tooltip-inner').html(title)
      $tip.removeClass('fade in top bottom left right')
    }

  , hide: function () {
      var that = this
        , $tip = this.tip()

      $tip.removeClass('in')

      function removeWithAnimation() {
        var timeout = setTimeout(function () {
          $tip.off($.support.transition.end).remove()
        }, 500)

        $tip.one($.support.transition.end, function () {
          clearTimeout(timeout)
          $tip.remove()
        })
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        removeWithAnimation() :
        $tip.remove()
    }

  , getPosition: function (inside) {
      return $.extend({}, (inside ? {top: 0, left: 0} : this.$element.offset()), {
        width: this.$element[0].offsetWidth
      , height: this.$element[0].offsetHeight
      })
    }

  , getTitle: function () {
      var title
        , $e = this.$element
        , o = this.options
      var title = $('<div>');
      $e.parent().find('a:not(.action)').each(function () {
          title.append($(this).clone(true));
      });
      return title;
    }

  , tip: function () {
      return this.$tip = this.$tip || $(this.options.template)
    }

  , enable: function () {
      this.enabled = true
    }

  , disable: function () {
      this.enabled = false
    }

  , toggleEnabled: function () {
      this.enabled = !this.enabled
    }

  , toggle: function () {
      this[this.tip().hasClass('in') ? 'hide' : 'show']()
    }

  }


 /* ActionsTooltip PLUGIN DEFINITION
  * ========================= */

  $.fn.actionsTooltip = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('actionsTooltip')
        , options = typeof option == 'object' && option
      if (!data) $this.data('actionsTooltip', (data = new ActionsTooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.actionsTooltip.Constructor = ActionsTooltip

  $.fn.actionsTooltip.defaults = {
    animation: true
  , placement: 'top'
  , selector: false
  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
  , trigger: 'hover'
  , title: ''
  , delay: 0
  }

}(window.jQuery);