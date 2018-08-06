define([
    'ko',
    'Magento_Checkout/js/model/quote'
], function(
    ko,
    quote
) {

    function wrap(ctx, origin_func, func) {
        return function() {
            return func.call(ctx, origin_func, arguments);
        };
    };

    function getCanUseShippingAddr(quote) {
        return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
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
        canUseShippingAddress: ko.computed(function() {
            return !quote.swiftGiftUsed() && getCanUseShippingAddr(quote);
        })
    };
    return function(target) {
        if (window.SWIFT_GIFT_SHOW === true) {
            return target.extend(mixin);
        }
        return target;
    };
});
