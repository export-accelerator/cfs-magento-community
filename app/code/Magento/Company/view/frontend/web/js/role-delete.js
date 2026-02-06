/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'uiRegistry',
    'mage/translate'
], function ($, confirm, alert, registry) {
    'use strict';

    $.widget('mage.roleDelete', {

        options: {
            isAjax: false,
            deletable: false,
            deleteUrl: '',
            id: '',
            gridProvider: ''
        },

        /**
         * Create widget
         *
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * Bind listeners on elements
         *
         * @private
         */
        _bind: function () {
            this._on({
                'deleteRole': '_deleteRole'
            });
        },

        /**
         * Ajax for delete company role
         *
         * @private
         */
        _sendAjax: function (url) {
            var self = this,
                data = {
                    'id': this.options.id
                };

            if (!this.options.isAjax) {
                this.options.isAjax = true;

                $.ajax({
                    url: url,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,

                    /** @inheritdoc */
                    success: function (res) {
                        if (res.status === 'error') {
                            alert({
                                modalClass: 'restriction-modal-quote',
                                responsive: true,
                                innerScroll: true,
                                title: $.mage.__('Cannot Delete Role'),
                                content: res.message
                            });
                        } else {
                            registry.get(self.options.gridProvider).reload({
                                refresh: true
                            });
                        }
                    },

                    /** @inheritdoc */
                    complete: function () {
                        self.options.isAjax = false;
                    }
                });
            }
        },

        /**
         * Set popup for delete
         *
         * @private
         */
        _deleteRole: function (e) {
            var self = this,
                options;

            e.preventDefault();

            if (this.options.deletable) {
                options = {
                    modalClass: 'modal-slide popup-tree',
                    buttons: [{
                        text: $.mage.__('Delete'),
                        'class': 'action primary delete',

                        /** @inheritdoc */
                        click: function (event) {
                            self._sendAjax(self.options.deleteUrl);
                            this.closeModal(event);
                        }
                    }, {
                        text: $.mage.__('Cancel'),
                        'class': 'action secondary cancel',

                        /** @inheritdoc */
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }],
                    title: $.mage.__('Delete This Role?'),
                    content: $.mage.__('This action cannot be undone. Are you sure you want to delete this role?')
                };
                confirm(options);
            } else {
                alert({
                    modalClass: 'restriction-modal-quote',
                    responsive: true,
                    innerScroll: true,
                    title: $.mage.__('Cannot Delete Role'),
                    content: $.mage.__('This role cannot be deleted because users are assigned to it. Reassign the users to another role to continue.') // eslint-disable-line max-len
                });
            }
        }
    });

    return $.mage.roleDelete;
});
