var config = {
    config: {
        mixins: {
            'Magento_Customer/js/model/customer/address': {
                'Swiftgift_Gift/js/mixins/address-can-use-for-billing': true
            },
            'Magento_Checkout/js/model/new-customer-address': {
                'Swiftgift_Gift/js/mixins/address-can-use-for-billing': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Swiftgift_Gift/js/mixins/billing-address-can-use-shipping': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Swiftgift_Gift/js/mixins/shipping-information-visible': true
            },
            'Magento_Checkout/js/model/quote': {
                'Swiftgift_Gift/js/mixins/quote-swiftgift': true
            }
        }
    }
};
