define([
    'jquery',
    'uiRegistry'
], function(
    $,
    uiRegistry
) {
    return function(config, element) {
        var elem = $(element);
        var addtocart_form = elem.parents('#product_addtocart_form');
        var use_swift_gift = false;
        elem.on('click', function(e) {
            use_swift_gift = true;
            addtocart_form.submit();
            e.preventDefault();
            return false;
        });
        var btn_standard_elem = $('#product-addtocart-button');
        btn_standard_elem.on('click', function(e) {
            use_swift_gift = false;
        });
        function handleProductCartAddFormSubmit() {
            uiRegistry.get('localStorage').set('swift_gift_used_init_value', use_swift_gift);
            if (use_swift_gift) {
                location.href = checkout.checkoutUrl;
            }
        };
        $(document).on('ajax:addToCart', function(e, data) {
            handleProductCartAddFormSubmit();
        });
    };
});
