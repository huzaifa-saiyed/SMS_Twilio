 

define([
  'mage/utils/wrapper'
], function(wrapper) {
  'use strict';

  return function (createBillingAddressAction) {
    return wrapper.wrap(createBillingAddressAction, function(originalAction, addressData) {
      if (addressData.custom_attributes === undefined) {
        return originalAction();
      }

      if (addressData.custom_attributes['sms_alert']) {
        addressData.custom_attributes['sms_alert'] = {
          'attribute_code': 'sms_alert',
          'value': 'SMS Enabled',
          'status': 1
        }
      }

      return originalAction();
    });
  };
});