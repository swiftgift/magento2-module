define([
    'ko',
    'uiComponent',
    'uiRegistry',
], function(ko, uiComponent, uiRegistry) {

    if (window.SWIFT_GIFT_SHOW !== true) {
        return uiComponent;
    };
    
    return uiComponent.extend({
        registry: uiRegistry,
        swiftGiftActive: ko.observable(false),
        initialize: function() {
            this._super();
            var self = this;
            this.registry.get('checkout.steps.shipping-step.shippingAddress', function(r) {
                self.swiftGiftActive(r.swiftGiftActive());
                self.swiftGiftActive = r.swiftGiftActive;
            });
        }
    });
});
