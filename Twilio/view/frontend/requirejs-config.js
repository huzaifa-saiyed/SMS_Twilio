var config = {
  config: {
    mixins: {
      'Magento_Checkout/js/action/set-shipping-information': {
        'Kitchen365_Twilio/js/action/set-shipping-information-mixin': true
      },
      'Magento_Checkout/js/action/create-shipping-address': {
        'Kitchen365_Twilio/js/action/create-shipping-address-mixin': true
      },
      'Magento_Checkout/js/action/create-billing-address': {
        'Kitchen365_Twilio/js/action/create-billing-address-mixin': true
      },
      'Magento_Checkout/js/action/place-order': {
        'Kitchen365_Twilio/js/action/place-order-mixin': true
      },
      'Magento_Customer/js/model/customer/address': {
        'Kitchen365_Twilio/js/model/customer/address-mixin': true
      }
    }
  }
};