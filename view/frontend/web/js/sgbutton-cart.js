define([
    'jquery',
    'uiRegistry'
], function(
    $,
    uiRegistry
) {
    return function(config, element) {
        $(element).on('click', function(e) {
            uiRegistry.get('localStorage').set('swift_gift_used_init_value', true);
            location.href = checkout.checkoutUrl;
            e.preventDefault();
            return false;
        });
    };
});
