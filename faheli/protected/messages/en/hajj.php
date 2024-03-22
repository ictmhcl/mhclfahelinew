<?php
return [
  // Alerts
  'hajjRegistrationReceivedText' => '\'s Hajj Registration Form has been
  received by MHCL. MHCL shall send a message after checking the form and
  submitted documents. Please contact us during office hours if you do not
  receive a response in 24 hours.',
  'registerForHajj' => 'Register for Hajj',
  'registrationSubmittedAlert' => '\'s Hajj Registration Form has been
  received by MHCL. MHCL shall send a message after checking the form and
  submitted documents. Please contact us during office hours if you do not
  receive a response in 24 hours.',

  // Hajj Account View
  'hajjAccountDetails' => 'Hajj Account Details',
  'balance' => 'Balance',
  'currencySymbol' => 'MVR',
  'direction' => 'ltr',
  'hajjPrice' => 'Hajj Price',
  'position' => 'Slot',
  'position {position} {year}' => 'At position of Hijri year',
  'placementInfo {amt}' => 'Need bal. amt',
  'payOnline' => 'Pay Online',
  'onlinePayment' => 'Online Payment',
  'temporarilyDown' => 'Online payment system is temporarily down',
  'confirmSlot' => ' (Confirm Slot)',
  'fullPayment' => ' (Full Payment)',
  'anotherAmount' => 'Another amount',
  'enterAnotherAmount' => 'Enter amount',
  'depositAmount' => 'Deposit amount',
  'payAboveAmount' => 'Pay above amount',
  'paymentNote' => 'Note: Please read the '
    . CHtml::link('terms of use of this portal', Yii::app()
      ->createUrl('users/userAgreement'), ['style' => 'text-decoration: underline'])
    . ' before using this payment services. You may not use the payment service
    unless you agree to those terms. Payments through this online portal are
    provided by the respective banks and it is a requirement of the bank to
    accept these terms.',
  'forwardedToBankModal' => 'You are being forwarded to Bank gateway for payment',
  'forwardedToBankInfo' => 'Your browser will return you back to this page
  after. Please DO NOT use BACK button OR REFRESH button or their functions
  during the payment process.',
  'cancelBtn' => 'Cancel',
  'bmlGateway' => 'Bank of Maldives Gateway',
  'declinedMsg' => 'Bank responded "Declined". Please try with another card.',
  'gatewayError' => 'There was an error providing service. We shall resolve
  it ASAP. Please try again later',
  'paymentSuccess {amt} {msg}' => 'Payment for amt confirmed. Thank you. msg',
  'slotMsg' => 'It can take up to 15 minutes to see your Hajj Slot number on
  your account',

  // Hajj Registration View
  'contactInfo' => 'Contact Information',
  'nokFamilyPhone' => 'Next of Kin/Family Phone No',
  'emergencyContactInfo' => 'Emergency Contact Name & No',
  'emergencyNamePhone' => 'Name & Phone',
  'emergencyPhone' => 'Emergency Contact Phone',
  'emergencyPhonePlaceHolder' => 'Phone',
  'emergencyNamePlaceHolder' => 'Emergency Contact Name',
  'hajjInfo' => 'Hajj Info',
  'badhal_hajj' => 'Badhalu Hajjeh?',
  'group_name' => 'Group Leader ID Card Number',
  'groupLeaderNote' => 'If the group leader has been registered, accommodation
   and travel will be organized together for the group. Other pilgrims may
   join this group by using the same Group Leader ID Number, when filling their forms.',
  'application_form' => 'Hajj Reg. Form',
  'mahram_document' => 'Mahram Proof Document',
  'downloadFormInfo' => 'Please '
    . CHtml::link('download', Constants::REGISTRATION_PDF, [
      'target' => '_blank', 'id' => 'applicationFormDownloadLink',
      'style' => 'text-decoration: underline'
    ]) . ' an application form, fill and attach above.',
  'mahramInfo' => 'If the applicant is a female below 45 years at purported
  time of travel, a Mahram of the applicant must register at MHCL, and an
  English translation of the official government document proving the
  relation should be submitted above. This document may also be submitted
  later, upon confirmation of Hajj placement for a specific year.'
];
