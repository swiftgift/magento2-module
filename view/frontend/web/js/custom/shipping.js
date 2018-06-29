define([
    'jquery',
    'underscore',
    'ko',
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',    
    'Magento_Checkout/js/view/shipping',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/full-screen-loader',
    'uiRegistry',
    'mage/translate'
], function(
    $,
    _,
    ko,
    mage_storage,
    quote,
    stepNavigator,
    Shipping,
    PaymentService,
    methodConverter,
    checkoutData,
    customerData,
    customer,
    fullScreenLoader,
    uiRegistry,
    $t
) {
    
    var swift_gift_used_init_value_key = 'swift_gift_used_init_value';

    if (window.SWIFT_GIFT_SHOW !== true) {
        return Shipping;
    };
    
    return Shipping.extend({
        registry: uiRegistry,
        defaults: {
            template: 'Swiftgift_Gift/custom/shipping'
        },
        swiftGiftActive: ko.observable(),
        initialize: function() {
            this._super();
            var shipping_addr = checkoutData.getShippingAddressFromData();
            if (shipping_addr && shipping_addr.swift_gift_active === true && quote.shippingAddress()) {
                quote.shippingAddress().canUseForBilling(false);
                quote.billingAddress(null);
            }
            var self = this;
            this.swiftGiftActive.subscribe(function(val) {
                self._refreshBySwiftGift(val);
                self.source.set('shippingAddress.swift_gift_active', val);
            });
            var swift_gift_active = (uiRegistry.get('localStorage').get(swift_gift_used_init_value_key) === true);
            uiRegistry.get('localStorage').remove(swift_gift_used_init_value_key);
            swift_gift_active = swift_gift_active || this.source.get('shippingAddress.swift_gift_active') === true;
            
            this.swiftGiftActive(swift_gift_active);
            quote.swiftGiftUsed(swift_gift_active);
            return this;
        },
        _refreshBySwiftGift: function(val) {
            if (val === true) {
                this._prepareSwiftGiftEnable();
            } else {
                this._prepareSwiftGiftDisable();
            }
        },
        _prepareSwiftGiftEnable: function() {
        },
        _prepareSwiftGiftDisable: function() {
        },
        validateShippingInformation: function() {
            if (this.swiftGiftActive()) {

                var loginFormSelector = 'form[data-role=email-with-possible-login]';

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                    if (!emailValidationResult) {
                        $(loginFormSelector + ' input[name=username]').focus();
                        return false;
                    }
                }
                
                if (!quote.shippingMethod()) {
                    this.errorValidationMessage($t('Please specify a shipping method.'));
                    return false;
                }
                this.source.set('params.invalid', false);
                this.source.trigger('swiftgiftForm.data.validate');
                var isInvalid = this.source.get('params.invalid');
                if (isInvalid) {
                    this.focusInvalid();
                }
                return !isInvalid;
            } else {
                return this._super();
            }
        },
        setShippingInformation: function () {
            var self = this;
            if (this.swiftGiftActive() === true) {
                quote.shippingAddress().canUseForBilling(false);
                quote.billingAddress(null);
                if (this.validateShippingInformation()) {
                    fullScreenLoader.startLoader();
                    var shipping_addr_data = this.source.get('shippingAddress');
                    var url = '/rest/V1/swiftgift/enable/'+quote.getQuoteId()+'/';
                    mage_storage.post(
                        url,
                        JSON.stringify({
                            swiftGiftInfo: {
                                name: shipping_addr_data.swift_gift_name,
                                message: shipping_addr_data.swift_gift_message,
                                country_code: shipping_addr_data.country_id,
                                region: shipping_addr_data.region,
                                region_id: shipping_addr_data.region_id,
                                shipping_method_code: quote.shippingMethod()['method_code'],
                                carrier_code: quote.shippingMethod()['carrier_code']
                            }
                        })).done(function(result) {
                            if (result.success === true) {
                                checkoutData.setSelectedBillingAddress(null);
                                checkoutData.setNewCustomerBillingAddress(null);
                                PaymentService.setPaymentMethods(methodConverter(result['payment_methods']));
                                quote.setTotals(result['totals']);
                                quote.swiftGiftUsed(true);
                                stepNavigator.next();
                            } else {
                                alert(result);
                            }
                            fullScreenLoader.stopLoader();
                        });
                }
            } else {
                quote.shippingAddress().canUseForBilling(true);
                quote.swiftGiftUsed(false);
                this._super();
            }
        }
    });
});
