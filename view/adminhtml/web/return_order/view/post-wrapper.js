define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, confirm) {
    'use strict';

    /**
     * @param {String} url
     * @param {String} method
     * @returns {jQuery}
     */
    function getForm(url, method) {
        return $('<form>', {
            'action': url,
            'method': method
        }).append($('<input>', {
            'name': 'form_key',
            'value': window.FORM_KEY,
            'type': 'hidden'
        }));
    }

    $('#return-order-view-reject-button').click(function () {
        const msg = $.mage.__('Are you sure you want to reject this order?'),
            url = $('#return-order-view-reject-button').data('url'),
            method = 'POST';

        confirm({
            'content': msg,
            'actions': {

                /**
                 * 'Confirm' action handler.
                 */
                confirm: function () {
                    getForm(url, method).appendTo('body').trigger('submit');
                }
            }
        });

        return false;
    });

    $('#return-order-view-accept-button').click(function () {
        const msg = $.mage.__('Are you sure you want to accept this shopflix order?'),
            url = $('#return-order-view-accept-button').data('url'),
            method = 'POST';

        confirm({
            'content': msg,
            'actions': {

                /**
                 * 'Confirm' action handler.
                 */
                confirm: function () {
                    getForm(url, method).appendTo('body').trigger('submit');
                }
            }
        });

        return false;
    });

    $('#return-order-view-sync-button').click(function () {
        const msg = $.mage.__('Are you sure you want update the order?'),
            url = $('#return-order-view-sync-button').data('url'),
            method = 'POST';

        confirm({
            'content': msg,
            'actions': {

                /**
                 * 'Confirm' action handler.
                 */
                confirm: function () {
                    getForm(url, method).appendTo('body').trigger('submit');
                }
            }
        });

        return false;
    });

});
