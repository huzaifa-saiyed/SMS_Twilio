 

define([
  'jquery',
  'mage/utils/wrapper',
  'Magento_Checkout/js/model/quote'
], function($, wrapper, quote) {
  'use strict';

  return function (placeOrderAction) {
    return wrapper.wrap(placeOrderAction, function(originalAction) {
      var billingAddress = quote.billingAddress();

      if(billingAddress.customAttributes === undefined) {
        billingAddress.customAttributes = {};
      }

      if(billingAddress['extension_attributes'] === undefined) {
        billingAddress['extension_attributes'] = {};
      }

      try {
        var smsStatus = billingAddress.customAttributes['sms_alert'].status;
        var smsValue = billingAddress.customAttributes['sms_alert'].value;

        //TODO: this smells. the check for status stems from an attempt to
        // non-invasively display a user friendly custom attribute value in
        // the customer address-mixin.
        if(smsStatus == true) {
          billingAddress['extension_attributes']['sms_alert'] = smsStatus;
        }else if(smsValue == true) {
          billingAddress['extension_attributes']['sms_alert'] = smsValue
        }else {
          billingAddress['extension_attributes']['sms_alert'] = false;
        }
      }catch (e) {
        return originalAction();
      }

      return originalAction();
    });
  };
});