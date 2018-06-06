define([
], function(
) {
    'use strict';    
    function wrap(ctx, origin_func, func) {
        return function() {
            return func.call(ctx, origin_func, arguments);
        };
    };

    return function(target) {
        return function(addressData) {
            var obj = target(addressData);
            var can_use_for_billing = null;
            obj.canUseForBilling = wrap(obj, obj.canUseForBilling, function(origin_func, args) {
                if (args.length > 0) {
                    can_use_for_billing = (args[0] === true);
                }
                return can_use_for_billing !== null ? can_use_for_billing : origin_func();
            });
            return obj;
        };
    };
});
