define([
    'ko',
    'underscore'
], function(
    ko,
    _
) {

    var mixin = {
        swiftGiftUsed: ko.observable(false)
    };
    
    return function(target) {
        return _.extend(target, mixin);
    };
});
