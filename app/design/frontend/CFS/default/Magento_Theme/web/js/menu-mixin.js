

define([
    'jquery',
    'matchMedia',
    'jquery-ui-modules/menu',
    'mage/translate'
],
function($, mediaCheck){
    return function() {
    $.widget('mage.mobmenu', $.ui.menu, {
        options: {
            responsive: false,
            expanded: false,
            showDelay: 42,
            hideDelay: 300,
            delay: 0,
            mediaBreakpoint: '(max-width: 768px)'
        },
        _create: function () {
            var self = this;

            this.delay = this.options.delay;

            this._super();
            $(window).on('resize', function () {
                self.element.find('.submenu-reverse').removeClass('submenu-reverse');
            });
        },
        _init: function () {
            this._super();

            if (this.options.expanded === true) {
                this.isExpanded();
            }

            if (this.options.responsive === true) {
                mediaCheck({
                    media: this.options.mediaBreakpoint,
                    entry: $.proxy(function () {
                        this._toggleMobileMode();
                    }, this),
                    exit: $.proxy(function () {
                        this._toggleDesktopMode();
                    }, this)
                });
            }

            this._assignControls()._listen();          
            this._setActiveMenu();
        },
        
        _assignControls: function () {
            this.controls = {
                toggleBtn: $('[data-action="toggle-nav"]')
            };

            return this;
        }, 
        _listen: function () {
            var controls = this.controls,
                toggle = this.toggle;

            controls.toggleBtn.off('click');
            controls.toggleBtn.on('click', toggle.bind(this));
        },
        toggle: function () {
            var html = $('html');

            if (html.hasClass('nav-open')) {
                html.removeClass('nav-open');
                setTimeout(function () {
                    html.removeClass('nav-before-open');
                }, this.options.hideDelay);
            } else {
                html.addClass('nav-before-open');
                setTimeout(function () {
                    html.addClass('nav-open');
                }, this.options.showDelay);
            }
        },
        _setActiveMenu: function () {
            var currentUrl = window.location.href.split('?')[0];

            if (!this._setActiveMenuForCategory(currentUrl)) {
                this._setActiveMenuForProduct(currentUrl);
            }
        },
        _setActiveMenuForCategory: function (url) {
            var activeCategoryLink = this.element.find('a[href="' + url + '"]'),
                classes,
                classNav;

            if (!activeCategoryLink || !activeCategoryLink.hasClass('ui-menu-item-wrapper')) {

                //category was not found by provided URL
                return false;
            } else if (!activeCategoryLink.parent().hasClass('active')) {
                activeCategoryLink.parent().addClass('active');
                classes = activeCategoryLink.parent().attr('class');
                classNav = classes.match(/(nav\-)[0-9]+(\-[0-9]+)+/gi);

                if (classNav) {
                    this._setActiveParent(classNav[0]);
                }
            }

            return true;
        },
        _setActiveParent: function (childClassName) {
            var parentElement,
                parentClass = childClassName.substr(0, childClassName.lastIndexOf('-'));

            if (parentClass.lastIndexOf('-') !== -1) {
                parentElement = this.element.find('.' + parentClass);

                if (parentElement) {
                    parentElement.addClass('has-active');
                }
                this._setActiveParent(parentClass);
            }
        },
        _setActiveMenuForProduct: function (currentUrl) {
            var categoryUrlExtension,
                lastUrlSection,
                possibleCategoryUrl,
                //retrieve first category URL to know what extension is used for category URLs
                firstCategoryUrl = this.element.find('> li a').attr('href');

            if (firstCategoryUrl) {
                lastUrlSection = firstCategoryUrl.substr(firstCategoryUrl.lastIndexOf('/'));
                categoryUrlExtension = lastUrlSection.lastIndexOf('.') !== -1 ?
                    lastUrlSection.substr(lastUrlSection.lastIndexOf('.')) : '';

                possibleCategoryUrl = currentUrl.substr(0, currentUrl.lastIndexOf('/')) + categoryUrlExtension;
                this._setActiveMenuForCategory(possibleCategoryUrl);
            }
        },
        isExpanded: function () {
            var subMenus = this.element.find(this.options.menus),
                expandedMenus = subMenus.find(this.options.menus);

            expandedMenus.addClass('expanded');
        },

        _activate: function (event) {
            window.location.href = this.active.find('> a').attr('href');
            this.collapseAll(event);
        },
        
        
        _toggleMobileMode: function () {
            var subMenus;

            $(this.element).off('mouseenter mouseleave');
            this._on({

                /**
                 * @param {jQuery.Event} event
                 */
                'click .ui-menu-item:has(a)': function (event) {
                    var target;
                    
                    event.preventDefault();
                    target = $(event.target).closest('.ui-menu-item');
                    target.get(0).scrollIntoView();

                    // Open submenu on click
                    if (target.has('.ui-menu').length) {
                        this.expand(event);
                    } else if (!this.element.is(':focus') && $(this.document[0].activeElement).closest('.ui-menu').length) {
                        // Redirect focus to the menu
                        this.element.trigger('focus', [true]);

                        // If the active item is on the top level, let it stay active.
                        // Otherwise, blur the active item since it is no longer visible.
                        if (this.active && this.active.parents('.ui-menu').length === 1) { //eslint-disable-line
                            clearTimeout(this.timer);
                        }
                    }

                    if (!target.hasClass('parent') || !target.has('.ui-menu').length) {
                        window.location.href = target.find('> a').attr('href');
                    }
                    
                },

                /**
                 * @param {jQuery.Event} event
                 */
                'click .ui-menu-item:has(.ui-state-active)': function (event) {
                    this.collapseAll(event, false);
                }
            });

            subMenus = this.element.find('.parent');
            $.each(subMenus, $.proxy(function (index, item) {
                var category = $(item).find('> a span').not('.ui-menu-icon').text(),
                    categoryUrl = $(item).find('> a').attr('href'),
                    menu = $(item).find('> .ui-menu');

                this.categoryLink = $('<a>')
                    .attr('href', categoryUrl)
                    .text($.mage.__('All %1').replace('%1', category));

                this.categoryParent = $('<li>')
                    .addClass('ui-menu-item all-category')
                    .html(this.categoryLink);

                if (menu.find('.all-category').length === 0) {
                    menu.prepend(this.categoryParent);
                }

            }, this));
        },
        _toggleDesktopMode: function () {
            var categoryParent, html;

            $(this.element).off('click mousedown mouseenter mouseleave');
            this._on({

                /**
                 * Prevent focus from sticking to links inside menu after clicking
                 * them (focus should always stay on UL during navigation).
                 */
                'mousedown .ui-menu-item > a': function (event) {
                    event.preventDefault();
                },

                /**
                 * Prevent focus from sticking to links inside menu after clicking
                 * them (focus should always stay on UL during navigation).
                 */
                'click .ui-state-disabled > a': function (event) {
                    event.preventDefault();
                },

                /**
                 * @param {jQuer.Event} event
                 */
                'click .ui-menu-item:has(a)': function (event) {
                    var target = $(event.target).closest('.ui-menu-item');

                    if (!this.mouseHandled && target.not('.ui-state-disabled').length) {
                        this.select(event);

                        // Only set the mouseHandled flag if the event will bubble, see #9469.
                        if (!event.isPropagationStopped()) {
                            this.mouseHandled = true;
                        }

                        // Open submenu on click
                        if (target.has('.ui-menu').length) {
                            this.expand(event);
                        } else if (!this.element.is(':focus') &&
                            $(this.document[0].activeElement).closest('.ui-menu').length
                        ) {
                            // Redirect focus to the menu
                            this.element.trigger('focus', [true]);

                            // If the active item is on the top level, let it stay active.
                            // Otherwise, blur the active item since it is no longer visible.
                            if (this.active && this.active.parents('.ui-menu').length === 1) { //eslint-disable-line
                                clearTimeout(this.timer);
                            }
                        }
                    }
                },

                /**
                 * @param {jQuery.Event} event
                 */
                'mouseenter .ui-menu-item': function (event) {
                    var target = $(event.currentTarget),
                        submenu = this.options.menus,
                        ulElement,
                        ulElementWidth,
                        width,
                        targetPageX,
                        rightBound;

                    if (target.has(submenu)) {
                        ulElement = target.find(submenu);
                        ulElementWidth = ulElement.outerWidth(true);
                        width = target.outerWidth() * 2;
                        targetPageX = target.offset().left;
                        rightBound = $(window).width();

                        if (ulElementWidth + width + targetPageX > rightBound) {
                            ulElement.addClass('submenu-reverse');
                        }

                        if (targetPageX - ulElementWidth < 0) {
                            ulElement.removeClass('submenu-reverse');
                        }
                    }

                    // Remove ui-state-active class from siblings of the newly focused menu item
                    // to avoid a jump caused by adjacent elements both having a class with a border
                    target.siblings().children('.ui-state-active').removeClass('ui-state-active');
                    this.focus(event, target);
                },

                /**
                 * @param {jQuery.Event} event
                 */
                'mouseleave': function (event) {
                    this.collapseAll(event, true);
                },

                /**
                 * Mouse leave.
                 */
                'mouseleave .ui-menu': 'collapseAll'
            });

            categoryParent = this.element.find('.all-category');
            html = $('html');

            categoryParent.remove();

            if (html.hasClass('nav-open')) {
                html.removeClass('nav-open');
                setTimeout(function () {
                    html.removeClass('nav-before-open');
                }, this.options.hideDelay);
            }
        },

        _delay: function (handler, delay) {
            var instance = this,

                /**
                 * @return {*}
                 */
                handlerProxy = function () {
                    return (typeof handler === 'string' ? instance[handler] : handler).apply(instance, arguments);
                };

            return setTimeout(handlerProxy, delay || 0);
        },

        expand: function (event) {
            var newItem = this.active &&
                this.active
                    .children('.ui-menu')
                    .children('.ui-menu-item')
                    .first();

            if (newItem && newItem.length) {
                if (newItem.closest('.ui-menu').is(':visible') &&
                    newItem.closest('.ui-menu').has('.all-categories')
                ) {
                    return;
                }

                // remove the active state class from the siblings
                this.active.siblings().children('.ui-state-active').removeClass('ui-state-active');

                this._open(newItem.parent());

                // Delay so Firefox will not hide activedescendant change in expanding submenu from AT
                this._delay(function () {
                    this.focus(event, newItem);
                });
            }
        },
        select: function (event) {
            var ui;

            this.active = this.active || $(event.target).closest('.ui-menu-item');

            if (this.active.is('.all-category')) {
                this.active = $(event.target).closest('.ui-menu-item');
            }
            ui = {
                item: this.active
            };

            if (!this.active.has('.ui-menu').length) {
                this.collapseAll(event, true);
            }
            this._trigger('select', event, ui);
        }
    });
       
        return $.mage.mobmenu
    }; 
});