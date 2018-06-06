define([
    'jquery',
    'uiComponent',
    'uiRegistry'
], function(
    $,
    uiComponent,
    uiRegistry
) {
    return uiComponent.extend({
        defaults: {
            template: 'Swiftgift_Gift/sgbutton-minicart.html'
        },
        initialize: function() {
            return this._super();
        },
        sendAsGift: function() {
            uiRegistry.get('localStorage').set('swift_gift_used_init_value', true);
            location.href = checkout.checkoutUrl;
        }
    });
});
