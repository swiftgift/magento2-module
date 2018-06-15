define([
    'ko',
    'underscore'
], function(
    ko,
    _
) {

    console.log('quote_mixin_called');
    var mixin = {
        swiftGiftUsed: ko.observable(false)
    };
    
    return function(target) {
        console.log('extended');
        return _.extend(target, mixin);
    };
});
