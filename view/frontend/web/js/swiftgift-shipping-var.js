define([
    'ko',
    'uiComponent',
    'uiRegistry',
], function(ko, uiComponent, uiRegistry) {    
    return uiComponent.extend({
        registry: uiRegistry,
        swiftGiftActive: ko.observable(false),
        initialize: function() {
            this._super();
            if (window.SWIFT_GIFT_SHOW !== true) {
                return;
            };
            var self = this;
            this.registry.get('checkout.steps.shipping-step.shippingAddress', function(r) {
                self.swiftGiftActive(r.swiftGiftActive());
                self.swiftGiftActive = r.swiftGiftActive;
            });
        }
    });
});
