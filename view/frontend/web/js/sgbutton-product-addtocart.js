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
        var loading_cls = 'loading';
        elem.on('click', function(e) {
            addtocart_form.addClass(loading_cls);
            elem.prop('disabled', true);
            $.post(
                addtocart_form.attr('action'),
                addtocart_form.serialize()
            ).then(function(result) {
                uiRegistry.get('localStorage').set('swift_gift_used_init_value', true);
                elem.prop('disabled', false);
                addtocart_form.removeClass(loading_cls);
                location.href = checkout.checkoutUrl;
            });
            e.preventDefault();
            return false;
        });
    };
});
