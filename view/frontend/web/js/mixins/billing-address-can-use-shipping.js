define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data'    
], function(
    ko,
    quote,
    checkoutData
) {

    function wrap(ctx, origin_func, func) {
        return function() {
            return func.call(ctx, origin_func, arguments);
        };
    };
    
    var mixin = {
        initObservable: function() {
            this._super();
            var obj = this;
            obj.isAddressDetailsVisible = wrap(obj, obj.isAddressDetailsVisible, function(origin_func, args) {
                if (typeof(args[0]) !== 'undefined') {
                    return origin_func(args[0] && quote.billingAddress() !== null);
                } else {
                    return origin_func();
                }
            });
            return this;
        },
        canUseShippingAddress: function() {
            return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
        }
    };
    return function(target) {
        return target.extend(mixin);
    };
});
