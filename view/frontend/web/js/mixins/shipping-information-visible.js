define([
    'Magento_Checkout/js/checkout-data'
], function(
    checkoutData
) {
    return function(target) {
        return target.extend({
            isVisible: function() {
                var sa = checkoutData.getShippingAddressFromData();
                return !(sa && sa.swift_gift_active === true) && this._super();
            }
        });
    };
});
