/*
 * ImageGallery - jQuery Plugin
 * A jQuery image gallery
 *
 * Copyright (c) 2011 Kristof Mariën
 *
 * Version: 1.1.5 (14/01/2012)
 * Requires: jQuery v1.7 or later
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */
(function ($) {
    var defaults = {
        zIndex                  : 1000,
        backgroundOpacity       : 0.9,
        fadeSpeed               : 200,
        thumbSize               : 100,
        thumbOpacity            : 0.5,
        thumbOpacityActive      : 0.9,
        thumbFadeSpeed          : 200,
        thumbScrollSpeed        : 500,
        thumbScrollTransition   : 'swing',
        imagePadding            : 20,
        imageFadeSpeed          : 500,
        imageFadeTransition     : 'swing',
        playInterval            : 5000,
        backgroundColor         : '#000000',
        timerBarHeight          : 2,
        timerBarColor           : '#ffffff',
        timerBarOpacity         : 0.5,

        closeText               : 'Close',
        previousText            : 'Previous',
        playText                : 'Play',
        pauzeText               : 'Pauze',
        nextText                : 'Next',
        censorText              : 'Censor',
        uncensorText            : 'Uncensor',

        allowCensor             : 0,
        censorUrl               : '',
        uncensorUrl             : '',

        imageSelector           : 'a.imageGallery',

        canPlay                 : true,
        showThumbBar            : true,
        preloadImages           : true
    };

    var methods = {
        init : function ( options ) {
            options = $.extend(defaults, options);

            return this.each(function () {
                var $this = $(this),
                    data = $this.data('iG');

                if ( ! data ) {
                    var number = 0;
                    $this.find(options.imageSelector)
                        .unbind('click.iG')
                        .bind('click.iG', _open)
                        .data('iG', {
                                container: $this
                            }
                        );
                    $this.data('iG', {
                            options: options
                        }
                    );
                }
            });
        },
        open : function ( image ) {
            var options = $(this).data('iG').options;
            if (undefined === image)
                $(this).find(options.imageSelector + ':first').click();
            else
                image.click();

            return this;
        },
        close : function () {
            _close();
            return this;
        },
        destroy : function () {
            return this.each(function () {
                _close();

                $(this).unbind('click.iG')
                    .removedata('iG');
            });
        },
        nextImage : function () {
            _next();
            return this;
        },
        previousImage: function () {
            _previous();
            return this;
        },
        play: function () {
            _play();
            return this;
        },
        pauze: function () {
            _pauze();
            return this;
        }
    };

    $.fn.imageGallery = function ( method ) {
        if ( methods[ method ] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.imageGallery' );
        }
    };

    _open = function (e) {
        e.preventDefault();

        var container = $(this).data('iG').container;
        var options = container.data('iG').options;

        _create(container, options);

        var $this = $(this);
        setTimeout(function() {_openImage($this)}, options.fadeSpeed);
    };

    _create = function (container, options) {
        if ($('#container-iG').length > 0 && $('#container-iG').data('iG').container == container)
            return;

        _close();

        $('body').append(
            imageGallery = $('<div>', {
                        id: 'container-iG'
                    }
                )
        );

        $(document).bind('keydown.iG', _keyDown);
        $(window).bind('resize.iG', _position);

        imageGallery
            .css(
                {
                    zIndex: options.zIndex
                }
            )
            .append(
                background = $('<div>', {
                            'class': 'background-iG'
                        }
                    )
                    .css(
                        {
                            opacity: options.backgroundOpacity,
                            background: options.backgroundColor
                        }
                    ),
                controls = $('<div>', {
                            'class': 'controls-iG'
                        }
                    ),
                loading = $('<div>', {
                            'class': 'loading-iG'
                        }
                    )
                    .append(
                        throbber = $('<div>', {
                                    'class': 'throbber-iG'
                                }
                            )
                    )
                    .css(
                        {
                            zIndex: options.zIndex + 10
                        }
                    )
            )
            .data('iG', {
                    options: options,
                    container: container
                }
            );

        if (options.allowCensor) {
            controls.append(
                censorControl = $('<div>', {
                            'class': 'button-iG censorControl-iG'
                        }
                    ).html(options.censorText)
                    .hide()
                    .unbind('click.iG')
                    .bind('click.iG', _censorImage),
                uncensorControl = $('<div>', {
                            'class': 'button-iG uncensorControl-iG'
                        }
                    ).html(options.uncensorText)
                    .hide()
                    .unbind('click.iG')
                    .bind('click.iG', _uncensorImage)
            );
        }

        controls.append(
            viewControls = $('<div>', {
                        'class': 'viewControls-iG'
                    }
                ),
            closeControl = $('<div>', {
                        'class': 'button-iG closeControl-iG'
                    }
                )
                .html('x' + (options.closeText.length > 0 ? ' ' + options.closeText : ''))
                .unbind('click.iG')
                .bind('click.iG', _close)
        );


        viewControls.append(
            previousControl = $('<div>', {
                        'class': 'button-iG previousControl-iG'
                    }
                )
                .append(
                    $('<span>', {
                                'class': 'image'
                            }
                        ),
                    options.previousText
                )
                .unbind('click.iG')
                .bind('click.iG', _previous),
            playControl = $('<div>', {
                        'class': 'button-iG playControl-iG startStopControl-iG'
                    }
                )
                .append(
                    $('<span>', {
                                'class': 'image'
                            }
                        ),
                    $('<span>', {
                                'class': 'text'
                            }
                        )
                        .html(options.playText)
                )
                .unbind('click.iG')
                .bind('click.iG', _startStop),
            nextControl = $('<div>', {
                        'class': 'button-iG nextControl-iG'
                    }
                )
                .append(
                    options.nextText,
                    $('<span>', {
                                'class': 'image'
                            }
                        )
                )
                .unbind('click.iG')
                .bind('click.iG', _next)
        );
        if (! options.canPlay)
            playControl.remove();

        _positionControls();

        if (options.showThumbBar)
            _createThumbBar(container, options);

        imageGallery.fadeOut(0).fadeIn(options.fadeSpeed);
        _showLoading();
    };

    _createThumbBar = function (container, options) {
        $('#container-iG').append(
            thumbBar = $('<div>', {
                        'class': 'thumbBar-iG'
                    }
                )
        );

        thumbBar.css({height: options.thumbSize})
            .append(
                scrollLeft = $('<div>', {
                            'class': 'scrollLeft-iG scroll-iG button-iG'
                        }
                    ),
                thumbContainer = $('<div>', {
                            'class': 'thumbs-iG'
                        }
                    ),
                scrollRight = $('<div>', {
                            'class': 'scrollRight-iG scroll-iG button-iG'
                        }
                    )
            );

        scrollLeft.css({
                    height: options.thumbSize
                }
            )
            .append(
                $('<div>', {
                            'class': 'image'
                        }
                    )
            )
            .bind('click.iG', _thumbsScrollLeft);
        scrollRight.css({
                    height: options.thumbSize
                }
            )
            .append(
                $('<div>', {
                            'class': 'image'
                        }
                    )
            )
            .bind('click.iG', _thumbsScrollRight);
        thumbContainer.css({
                    height: options.thumbSize
                }
            )
            .append(
                thumbs = $('<ul>')
            );

        var totalWidth = 3;
        container.find(options.imageSelector).each(function () {
            thumbs.append(
                thumb = $('<li>')
                    .data('iG',
                        {
                            image: $(this)
                        }
                    )
            );
            thumb.html(
                $('<img>', {
                            src: $(this).find('img').attr('src')
                        }
                    )
                    .css(
                        {
                            maxWidth: options.thumbSize,
                            maxHeight: options.thumbSize
                        }
                    )
            );
            totalWidth += options.thumbSize + 3;
        });
        thumbs.width(totalWidth)
            .find('li')
                .css(
                    {
                        opacity: options.thumbOpacity,
                        width: options.thumbSize,
                        height: options.thumbSize
                    }
                )
                .mouseover(function () {
                        if ($(this).hasClass('activeThumb-iG'))
                            return;
                        $(this).stop()
                            .fadeTo(options.thumbFadeSpeed, options.thumbOpacityActive);
                    }
                )
                .mouseout(function () {
                        if ($(this).hasClass('activeThumb-iG'))
                            return;
                        $(this).stop()
                            .fadeTo(options.thumbFadeSpeed, options.thumbOpacity);
                    }
                )
                .click(function () {
                        _pauze();
                        _openImage($(this).data('iG').image);
                    }
                );
    };

    _thumbsScrollLeft = function () {
        var options = $('#container-iG').data('iG').options;
        var thumbs = $('#container-iG .thumbs-iG');

        var left = _calculateThumbBarLeft(thumbs.find('ul').position().left + ( thumbs.width() / 3 * 2 ));

        thumbs.find('ul')
                .stop()
                .animate({left: left}, options.thumbScrollSpeed, options.thumbScrollTransition);
    };

    _thumbsScrollRight = function () {
        var options = $('#container-iG').data('iG').options;
        var thumbs = $('#container-iG .thumbs-iG');

        var left = _calculateThumbBarLeft(thumbs.find('ul').position().left - ( thumbs.width() / 3 * 2 ));

        thumbs.find('ul')
                .stop()
                .animate({left: left}, options.thumbScrollSpeed, options.thumbScrollTransition);
    };

    _openImage = function (image) {
        if (! image || 0 == image.length) {
            $.error( 'The given image was not valid for jQuery.imageGallery' );
            return;
        }

        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        imageGallery.data('iG').current = image;

        imageGallery.find('.image-iG').addClass('previous-iG');

        imageGallery.append(
            imageHolder = $('<img>',
                    {
                        'class': 'image-iG',
                        src: image.attr('href')
                    }
                )
                .hide()
                .css(
                    {
                        zIndex: options.zIndex + 5
                    }
                )
        );

        _showLoading();
        imageHolder.load(function () {
                _hideLoading();

                var $this = $(this);
                $this.unbind('load');

                if (imageGallery.find('.previous-iG').length > 0) {
                    var playBar = imageGallery.find('.playBar-iG');

                    if (playBar.length > 0)
                        playBar.fadeOut(options.imageFadeSpeed);

                    imageGallery.find('.previous-iG').fadeOut(options.imageFadeSpeed, function () {
                                $(this).remove();
                                $this.fadeIn(options.imageFadeSpeed, options.imageFadeTransition);
                                _position();

                                if (playBar.length > 0)
                                    _startPlayBar();
                            }
                        );
                } else {
                    $this.fadeIn(options.imageFadeSpeed, options.imageFadeTransition);
                    _position();
                }

                if (options.preloadImages) {
                    var next = image.next(options.imageSelector);
                    var previous = image.prev(options.imageSelector);

                    if (next)
                        $('<img>', {src: next.attr('href')});
                    if (previous)
                        $('<img>', {src: previous.attr('href')});
                }
            }
        );

        if (0 == image.next(options.imageSelector).length)
            imageGallery.find('.nextControl-iG')
                .addClass('button-disabled-iG');
        else
            imageGallery.find('.nextControl-iG')
                .removeClass('button-disabled-iG');

        if (0 == image.prev(options.imageSelector).length)
            imageGallery.find('.previousControl-iG')
                .addClass('button-disabled-iG');
        else
            imageGallery.find('.previousControl-iG')
                .removeClass('button-disabled-iG');

        if (image.hasClass('censored')) {
            imageGallery.find('.censorControl-iG').hide();
            imageGallery.find('.uncensorControl-iG').show();
        } else {
            imageGallery.find('.censorControl-iG').show();
            imageGallery.find('.uncensorControl-iG').hide();
        }

        if (options.showThumbBar) {
            var thumbs = imageGallery.find('.thumbs-iG');
            var currentImage = imageGallery.data('iG').current;

            thumbs.find('ul li')
                    .removeClass('activeThumb-iG')
                    .stop()
                    .fadeTo(options.thumbFadeSpeed, options.thumbOpacity);

            if (undefined != currentImage) {
                var currentThumb = $(thumbs.find('ul li').get(currentImage.index()))
                        .addClass('activeThumb-iG')
                        .stop()
                        .fadeTo(options.thumbFadeSpeed, options.thumbOpacityActive);
                var left = _calculateThumbBarLeft(thumbs.width() / 2 - currentThumb.position().left - options.thumbSize / 2);

                thumbs.find('ul')
                    .stop()
                    .animate({left: left}, options.thumbScrollSpeed, options.thumbScrollTransition);
            }
        }
    };

    _next = function () {
        _pauze();
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        var next = imageGallery.data('iG').current.next(options.imageSelector);

        if (next.length > 0)
            _openImage(next);
    };

    _previous = function () {
        _pauze();
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        var previous = imageGallery.data('iG').current.prev(options.imageSelector);

        if (previous.length > 0)
            _openImage(previous);
    };

    _loop = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        var next = imageGallery.data('iG').current.next(options.imageSelector);

        if (next.length > 0)
            _openImage(next);
        else
            _openImage(imageGallery.data('iG').container.find(options.imageSelector).first());
    };

    _startStop = function (e) {
        if ($('#container-iG .startStopControl-iG').hasClass('playControl-iG'))
            _play();
        else
            _pauze();
    };

    _play = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        if (! options.canPlay)
            return;

        imageGallery.find('.playControl-iG')
            .removeClass('playControl-iG')
            .addClass('pauzeControl-iG')
            .find('.text')
                .html(options.pauzeText);

        _positionControls();

        imageGallery.data('iG').playTimer = setInterval(_loop, options.playInterval);

        imageGallery.append(
            playBar = $('<div>',
                    {
                        'class': 'playBar-iG'
                    }
                )
                .css(
                    {
                        height: options.timerBarHeight
                    }
                )
        );

        playBar.append(
            progress = $('<div>')
                .css(
                    {
                        height: options.timerBarHeight,
                        width: 0,
                        background: options.timerBarColor,
                        opacity: options.timerBarOpacity
                    }
                )
        );

        _startPlayBar();
    };

    _pauze = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        imageGallery.find('.pauzeControl-iG')
            .removeClass('pauzeControl-iG')
            .addClass('playControl-iG')
            .find('.text')
                .html(options.playText);

        imageGallery.find('.playBar-iG').remove();

        _positionControls();

        clearInterval(imageGallery.data('iG').playTimer);
    };

    _startPlayBar = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;
        var playBar = imageGallery.find('.playBar-iG');
        var progress = playBar.find('div');

        _positionPlayBar();

        playBar.show();

        progress.width(0)
            .css('percWidth', 0)
            .stop()
            .animate({percWidth: 1},
                    {
                        duration: options.playInterval,
                        easing: 'swing',
                        step: function(now, fx) {
                            $(this).width(now * playBar.width());
                        }
                    }
                );
    };

    _keyDown = function (e) {
        switch(e.keyCode) {
            case 27:
                e.preventDefault();
                _close();
                break;
            case 39:
                e.preventDefault();
                _next();
                break;
            case 37:
                e.preventDefault();
                _previous();
                break;
            case 32:
                e.preventDefault();
                _startStop();
                break;
        }
    };

    _showLoading = function () {
        clearInterval($('#container-iG').data('iG').loadingTimer);
        $('#container-iG').data('iG').loadingTimer = setInterval(_animateLoading, 70);

        _position();
        $('#container-iG .loading-iG').stop().fadeIn(100);
    };

    _hideLoading = function () {
        $('#container-iG .loading-iG').stop().fadeOut(100);
        clearInterval($('#container-iG').data('iG').loadingTimer);
    };

    _animateLoading = function () {
        var loading = $('#container-iG .loading-iG');

        if (! loading.is(':visible')) {
            clearInterval($('#container-iG').data('iG').loadingTimer);
            return;
        }

        loading.find('.throbber-iG')
            .css(
                {
                    top: ( loading.find('.throbber-iG').position().top - 40 ) % ( 40 * 12 )
                }
            );
    };

    _position = function () {
        var options = $('#container-iG').data('iG').options;
        options.showThumbBar = $(window).height() > 600;

        $('#container-iG .loading-iG').css(
                {
                    top: ( $(window).height() - options.thumbSize ) / 2 - 20,
                    left: $(window).width() / 2 - 20
                }
            );

        var image = $('#container-iG .image-iG');
        if (image.length > 0) {
            image.css(
                    {
                        maxHeight: $(window).height() - (options.showThumbBar ? options.thumbSize : 0) - 29 - ( options.imagePadding * 2 ),
                        maxWidth: $(window).width() - ( options.imagePadding * 2 )
                    }
                );
            image.css(
                    {
                        top: ( $(window).height() - (options.showThumbBar ? options.thumbSize : 0) - 29 ) / 2 - image.height() / 2 + 29,
                        left: $(window).width() / 2 - image.width() / 2
                    }
                );

            _positionPlayBar();
        }

        if (options.showThumbBar) {
            _positionThumbBar();
        } else {
            $('#container-iG .thumbs-iG').hide();
            $('#container-iG .scrollLeft-iG').hide();
            $('#container-iG .scrollRight-iG').hide();
        }
    };

    _positionControls = function () {
        var viewControls = $('#container-iG .viewControls-iG');
        var width = 0;
        viewControls.find('div').each(function () {
            width += $(this).outerWidth();
        });
        viewControls.width(width+1);
    };

    _positionPlayBar = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;
        var playBar = imageGallery.find('.playBar-iG');
        var image = imageGallery.find('.image-iG');

        if (playBar.length > 0) {
            playBar.css(
                    {
                        top: ( $(window).height() - options.thumbSize - 29 ) / 2 - image.height() / 2 + 29 - options.timerBarHeight,
                        left: $(window).width() / 2 - image.width() / 2,
                        width: image.width()
                    }
                );
        }
    };

    _positionThumbBar = function () {
        var imageGallery = $('#container-iG');
        var thumbs = imageGallery.find('.thumbs-iG');
        thumbs.show();

        if (thumbs.find('ul').width() < thumbs.width()) {
            $('#container-iG .scrollLeft-iG').hide();
            $('#container-iG .scrollRight-iG').hide();

            var left = thumbs.width() / 2 - thumbs.find('ul').width() / 2;
        } else {
            $('#container-iG .scrollLeft-iG').show();
            $('#container-iG .scrollRight-iG').show();

            var left = _calculateThumbBarLeft(thumbs.find('ul').position().left);
        }

        thumbs.find('ul')
            .stop()
            .css(
                {
                    left: left
                }
            );
    };

    _calculateThumbBarLeft = function (left) {
        var thumbs = $('#container-iG .thumbs-iG');

        if (thumbs.find('ul').width() < thumbs.width())
            left = thumbs.width() / 2 - thumbs.find('ul').width() / 2;
        else if (left > 0)
            return 0;
        else if (left < -thumbs.find('ul').width() + thumbs.width())
            return -thumbs.find('ul').width() + thumbs.width();
        return left;
    };

    _censorImage = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        if (options.allowCensor) {
            $.get(options.censorUrl + imageGallery.data('iG').current.data('id'), function (data) {
                if (data && 'success' == data.status) {
                    imageGallery.find('.censorControl-iG').hide();
                    imageGallery.find('.uncensorControl-iG').show();
                }
            }, 'json');
        }
    };

    _uncensorImage = function () {
        var imageGallery = $('#container-iG');
        var options = imageGallery.data('iG').options;

        if (options.allowCensor) {
            $.get(options.uncensorUrl + imageGallery.data('iG').current.data('id'), function (data) {
                if (data && 'success' == data.status) {
                    imageGallery.find('.censorControl-iG').show();
                    imageGallery.find('.uncensorControl-iG').hide();
                }
            }, 'json');
        }
    };

    _close = function () {
        var imageGallery = $('#container-iG');
        if (imageGallery.length == 0)
            return;

        var options = imageGallery.data('iG').options;
        $(document).unbind('keydown.iG');
        $(window).unbind('resize.iG');

        clearInterval(imageGallery.data('iG').loadingTimer);
        clearInterval(imageGallery.data('iG').playTimer);

        imageGallery.fadeOut(options.fadeSpeed, function () {
                $(this).remove();
            }
        );
    };
}) (jQuery)