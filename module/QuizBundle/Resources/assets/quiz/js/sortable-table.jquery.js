/*!
 * SortableTable
 * 
 * jQuery plugin to make a table of entries sortable and saveable to an url.
 * 
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 * @license MIT
 *
 * URL: https://github.com/vierbergenlars/snippets/blob/master/js/sortable-table.jquery.js 
 */
(function($) {
    "use strict";

    var SortableTable = function($table, options) {
        this.$table = $table.find('tbody');
        this.options = $.extend({}, $.fn.sortableTable.defaults, options);
        if(this.options.buttons) {
            this.$saveButton = $(this.options.buttons.save);
            this.$cancelButton = $(this.options.buttons.cancel);
            this._$buttons = $(this.$saveButton).add(this.$cancelButton);
        }
        this.attribute = this.options.attribute;
        this.key = this.options.key;
        this.submitUrl = this.options.submitUrl;
        this._originalPositions = [];
        this.callbacks = $.extend({}, $.fn.sortableTable.defaults.callbacks, this.options.callbacks)
        this.init();
    };

    SortableTable.prototype = {
        constructor: SortableTable,
        init: function() {
            this.callbacks.beforeInit();
            this.$table.sortable();
            this._originalPositions = this.$table.sortable('toArray', {attribute: this.attribute});
            this.$table.find('td').each(function() { // Force width on all td elements, so they don't collapse on drag.
                var $this = $(this);
                $this.width($this.width());
            });
            var that = this;
            this.$table.on('sortupdate', function() {that._onSortUpdate()});
            this.$saveButton.on('click', function() {that._onSortSave()});
            this.$cancelButton.on('click', function() {that._onSortCancel()});
            this.callbacks.afterInit();
        },
        destroy: function() {
            this.$table.sortable('destroy');
        },
        _onSortUpdate: function() {
            if(false === this.callbacks.beforeUpdate()) return;
            var updated = this.$table.sortable('toArray', {attribute: this.attribute});
            for(var i = 0; i < this._originalPositions.length; i++) {
                if(updated[i] !== this._originalPositions[i]) {
                    if(this._$buttons)
                        this._$buttons.show();
                    this.callbacks.afterUpdate();
                    return;
                }
            }
            if(this._$buttons)
                this._$buttons.hide();
            this.callbacks.afterUpdate();
        },
        _onSortSave: function() {
            if(false === this.callbacks.beforeSave()) return;
            var data  = this.$table.sortable('serialize', {
                key: this.key,
                attribute: this.attribute,
                expression: /(.*)/
            });
            var that = this;
            $.post(this.submitUrl, data, function(resp) { that._onSaveComplete(resp) }, 'json');
        },
        _onSortCancel: function() {
            if(false === this.callbacks.beforeCancel()) return;
            for(var i = 0; i < this._originalPositions.length; i++) {
                var $trs = this.$table.find('tr');
                var $tr = this.$table.find('tr['+this.attribute+'='+this._originalPositions[i]+']');
                if($trs.index($tr) === i) // This row is in the right place.
                    continue;
                $tr.detach();
                $tr.insertBefore($trs.eq(i));
            }
            if(this._$buttons)
                this._$buttons.hide();
            this.callbacks.afterCancel();
        },
        _onSaveComplete: function(data) {
            if(data && 'success' === data.status) {
                this._$buttons.hide();
                this.callbacks.saveSuccess();
            } else {
                this.callbacks.saveError();
            }
            this._originalPositions = this.$table.sortable('toArray', {attribute: this.attribute});
            this.callbacks.afterSave();
        }
    };

    $.fn.sortableTable = function(opts) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('sortableTable');
            if(typeof opts === 'object')
                var options = opts;
            if(!data) {
                data = new SortableTable($this, options);
                $this.data('sortableTable', data);
            }
            if(typeof opts === 'string') {
                data[opts]();
            }
        });
    };

    $.fn.sortableTable.defaults = {
        attribute: 'data-id',
        key: 'items[]',
        submitUrl: '',
        callbacks: {
            beforeInit: new Function,
            afterInit: new Function,
            beforeUpdate: new Function,
            afterUpdate: new Function,
            beforeSave: new Function,
            afterSave: new Function,
            saveSuccess: new Function,
            saveError: new Function,
            beforeCancel: new Function,
            afterCancel: new Function
        }
    };

    $.fn.sortableTable.constructor = SortableTable;
})(jQuery);
