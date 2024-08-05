 

define([
  'jquery',
  'mage/utils/wrapper',
  'mage/translate'
], function($, wrapper) {
  'use strict';

  return function (addressModel) {
    return wrapper.wrap(addressModel, function(originalAction) {
      var address = originalAction();

      if(address.customAttributes !== undefined) {
        if(address.customAttributes['sms_alert']) {
          var enabled = address.customAttributes['sms_alert'].value;
          address.customAttributes['sms_alert'].value = $.mage.__(enabled ? 'SMS Enabled' : 'SMS Disabled');
          address.customAttributes['sms_alert'].status = enabled ? 1 : 0;
        }
      }

      return address;
    });
  };
});