define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'ko'
], function(
    $,
    uiComponent,
    uiRegistry,
    ko
) {
    return uiComponent.extend({
        defaults: {
            template: 'Swiftgift_Gift/sgbutton-minicart.html'
        },
        initialize: function() {
            return this._super();
        },
        canShow: ko.computed(function() {
            return (window.SWIFT_GIFT_SHOW === true);
        }),
        sendAsGift: function() {
            uiRegistry.get('localStorage').set('swift_gift_used_init_value', true);
            location.href = checkout.checkoutUrl;
        }
    });
});
